<?php

namespace App\Domain\Services\Movimento;

use App\Models\Items;
use App\Core\Dtos\ItemDto;
use App\Models\Movimentos;
use Illuminate\Support\Collection;
use App\Data\Services\IDbTransaction;
use App\Core\Repositories\Item\IItemRepository;
use App\Core\ApplicationModels\JwtTokenProvider;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\Movimento\MovimentoEntradaRequest;
use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Core\Services\Movimento\IMovimentoEntradaService;

class MovimentoEntradaService implements IMovimentoEntradaService
{
    public function __construct(
        private JwtTokenProvider $jwtTokenProvider,
        private IMovimentoRepository $movimentoRepository,
        private IItemRepository $itemRepository,
        private IDbTransaction $dbTransaction,
    )
    {
    }
    private array $movimentacoesId;

    public function createMovimentacaoEntrada(MovimentoEntradaRequest $request): Collection
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Criar Movimentações');
        $this->movimentacoesId = [];
        $this->dbTransaction->run(function () use ($request) {
           foreach ($request->entradas as $entrada) {
                $item = $this->validateItem($entrada);
                $ItemforUpdate = $this->mapItemEntrada($entrada);
                $ItemforUpdate->estoque += $item->quantidadeEstoque;
                $this->itemRepository->updateItem($item->id, $ItemforUpdate);
                $newMovimentacao = $this->mapMovimentacaoEntrada($entrada);
                $movimentacao = $this->movimentoRepository->createMovimentacao($newMovimentacao);
                $this->movimentacoesId[] = $movimentacao->id;
           }
       });
       return $this->movimentoRepository->getMovimentacoesByIdList($this->movimentacoesId);
    }
    private function validateItem(array $entrada): ItemDto
    {
        $movimentoDto = $this->movimentoRepository->getMovimentacaoByNotaFiscal($entrada['notaFiscal']);
        if($movimentoDto){
            throw new HttpResponseException(response()->json(['message' => 'Nota fiscal ' .$entrada['notaFiscal']. ' já cadastrada.'], 400));
        }
        $item = $this->itemRepository->getItemById($entrada['itemId']);
        if(!$item) {
            throw new HttpResponseException(response()->json(['message' => 'Item' .$entrada['itemId']. 'não encotrado.'], 404));
        }
        return $item;
    }
    private function mapItemEntrada(array $request): Items
    {
        $ItemforUpdate = new Items();
        $ItemforUpdate->estoque = $request['quantidade'];
        return $ItemforUpdate;
    }
    private function mapMovimentacaoEntrada(array $request): Movimentos
    {
        $movimentacao = new Movimentos();
        $movimentacao->tipo = 'ENTRADA';
        $movimentacao->quantidade = $request['quantidade'];
        $movimentacao->data_movimentacao = $request['data'];
        $movimentacao->item_id = $request['itemId'];
        $movimentacao->user_id = auth()->user()->id;
        $movimentacao->nota_fiscal = $request['notaFiscal'];
        $movimentacao->fornecedor = $request['fornecedor'];
        return $movimentacao;
    }
}
