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
use App\Http\Requests\Movimento\MovimentoSaidaRequest;
use App\Core\Services\Movimento\IMovimentoSaidaService;
use App\Core\Repositories\Movimento\IMovimentoRepository;

class MovimentoSaidaService implements IMovimentoSaidaService
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

    public function createMovimentacaoSaida(MovimentoSaidaRequest $request): Collection
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Criar Movimentações');
        $this->movimentacoesId = [];
        $this->dbTransaction->run(function () use ($request) {
            foreach ($request->saidas as $saida) {
                $item = $this->validateItem($saida);
                $ItemforUpdate = $this->mapItemSaida($saida);
                $ItemforUpdate->estoque -= $item->quantidadeEstoque;
                $this->itemRepository->updateItem($item->id, $ItemforUpdate);
                $newMovimentacao = $this->mapMovimentacaoSaida($saida);
                $movimentacao = $this->movimentoRepository->createMovimentacao($newMovimentacao);
                $this->movimentacoesId[] = $movimentacao->id;
            }
        });
        return $this->movimentoRepository->getMovimentacoesByIdList($this->movimentacoesId);
    }
    private function validateItem(array $saida): ItemDto
    {
        $item = $this->itemRepository->getItemById($saida['itemId']);
        if(!$item) {
            throw new HttpResponseException(response()->json(['message' => 'Item' .$saida['itemId']. 'não encotrado.'], 404));
        }
        if($item->quantidadeEstoque < $saida['quantidade']){
            throw new HttpResponseException(response()->json(['message' => 'Quantidade insuficiente no estoque'], 400));
        }
        return $item;
    }
    private function mapItemSaida(array $saida): Items
    {
        $ItemforUpdate = new Items();
        $ItemforUpdate->estoque = $saida['quantidade'];
        return $ItemforUpdate;
    }
    private function mapMovimentacaoSaida(array $saida): Movimentos
    {
        $movimentacao = new Movimentos();
        $movimentacao->tipo = 'SAIDA';
        $movimentacao->quantidade = $saida['quantidade'];
        $movimentacao->data_movimentacao = $saida['data'];
        $movimentacao->item_id = $saida['itemId'];
        $movimentacao->user_id = auth()->user()->id;
        $movimentacao->numero_controle_saida = $saida['numeroControleSaida'];
        $movimentacao->local_destino = $saida['localDestino'];
        return $movimentacao;
    }
}
