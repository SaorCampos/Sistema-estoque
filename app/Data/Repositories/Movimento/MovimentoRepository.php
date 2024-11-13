<?php

namespace App\Data\Repositories\Movimento;

use App\Models\Movimentos;
use App\Core\Dtos\MovimentoDto;
use Illuminate\Support\Collection;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Http\Requests\Movimento\MovimentosListingRequest;

class MovimentoRepository implements IMovimentoRepository
{
    public function getAllMovimetacoes(MovimentosListingRequest $request, Pagination $pagination): PaginatedList
    {
        $query = Movimentos::from('movimentos as m')
            ->join('items as i', 'i.id', '=', 'm.item_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->select([
                'm.id as movimentacao_id',
                'i.id as item_id',
                'i.nome as nome_item',
                'i.estoque as quantidade_estoque',
                'm.tipo as tipo_movimentacao',
                'm.quantidade as quantidade_movimentada',
                'm.data_movimentacao',
                'm.nota_fiscal',
                'm.fornecedor',
                'm.numero_controle_saida',
                'm.local_destino',
                'u.name as usuario_responsavel',
                'm.criado_em',
                'm.atualizado_em',
                'm.atualizado_por',
            ])
            ->where($this->getFilters($request))
            ->paginate($pagination->perPage, ['*'], 'page', $pagination->page);
        return PaginatedList::fromPaginatedQuery(
            query: $query,
            pagination: $pagination,
            dtoClass: MovimentoDto::class
        );
    }
    private function getFilters(MovimentosListingRequest $request): array
    {
        $filters = [];
        if(!is_null($request->tipoMovimentacao)){
            $filters[] = ['m.tipo', '=', $request->tipoMovimentacao];
        }
        if(!is_null($request->nomeItem)){
            $filters[] = ['i.nome', 'ilike', '%' . $request->nomeItem . '%'];
        }
        if(!is_null($request->itemId)){
            $filters[] = ['m.item_id', '=', $request->itemId];
        }
        if(!is_null($request->dataMovimentacao)){
            $filters[] = ['m.data_movimentacao', '=', $request->dataMovimentacao];
        }
        if(!is_null($request->notaFiscal)){
            $filters[] = ['m.nota_fiscal', '=', $request->notaFiscal];
        }
        if(!is_null($request->fornecedor)){
            $filters[] = ['m.fornecedor', 'ilike', '%' . $request->fornecedor . '%'];
        }
        if(!is_null($request->localDestino)){
            $filters[] = ['m.local_destino', 'ilike', '%' . $request->localDestino . '%'];
        }
        if(!is_null($request->usuarioResponsavel)){
            $filters[] = ['u.name', 'ilike', '%' . $request->usuarioResponsavel . '%'];
        }
        if(!is_null($request->dataInicial)){
            $filters[] = ['m.data_movimentacao', '>=', $request->dataInicial];
        }
        if(!is_null($request->dataFinal)){
            $filters[] = ['m.data_movimentacao', '<=', $request->dataFinal . ' 23:59:59'];
        }
        return $filters;
    }
    public function createMovimentacao(Movimentos $movimento): Movimentos
    {
        return Movimentos::query()->create($movimento->toArray());
    }
    public function getMovimentoById(string $id): MovimentoDto
    {
        $movimento = Movimentos::from('movimentos as m')
            ->join('items as i', 'i.id', '=', 'm.item_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->select([
                'm.id as movimentacao_id',
                'i.id as item_id',
                'i.nome as nome_item',
                'i.estoque as quantidade_estoque',
                'm.tipo as tipo_movimentacao',
                'm.quantidade as quantidade_movimentada',
                'm.data_movimentacao',
                'm.nota_fiscal',
                'm.fornecedor',
                'm.numero_controle_saida',
                'm.local_destino',
                'u.name as usuario_responsavel',
                'm.criado_em',
                'm.atualizado_em',
                'm.atualizado_por',
            ])
            ->where('m.id', '=', $id)
            ->first();
        return $movimento->mapTo(MovimentoDto::class);
    }
    public function getMovimentacoesByIdList(array $ids): Collection
    {
        $resultCollection = Movimentos::from('movimentos as m')
            ->join('items as i', 'i.id', '=', 'm.item_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->select([
                'm.id as movimentacao_id',
                'i.id as item_id',
                'i.nome as nome_item',
                'i.estoque as quantidade_estoque',
                'm.tipo as tipo_movimentacao',
                'm.quantidade as quantidade_movimentada',
                'm.data_movimentacao',
                'm.nota_fiscal',
                'm.fornecedor',
                'm.numero_controle_saida',
                'm.local_destino',
                'u.name as usuario_responsavel',
                'm.criado_em',
                'm.atualizado_em',
                'm.atualizado_por',
            ])
            ->whereIn('m.id', $ids)
            ->get();
        foreach ($resultCollection as $key => $row) {
            $resultCollection[$key] = $row->mapTo(MovimentoDto::class);
        }
        return $resultCollection;
    }
    public function getMovimentacaoByNotaFiscal(int $notaFiscal): ?MovimentoDto
    {
        $movimento = Movimentos::from('movimentos as m')
            ->join('items as i', 'i.id', '=', 'm.item_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->select([
                'm.id as movimentacao_id',
                'i.id as item_id',
                'i.nome as nome_item',
                'i.estoque as quantidade_estoque',
                'm.tipo as tipo_movimentacao',
                'm.quantidade as quantidade_movimentada',
                'm.data_movimentacao',
                'm.nota_fiscal',
                'm.fornecedor',
                'm.numero_controle_saida',
                'm.local_destino',
                'u.name as usuario_responsavel',
                'm.criado_em',
                'm.atualizado_em',
                'm.atualizado_por',
            ])
            ->where('m.nota_fiscal', '=', $notaFiscal)
            ->first();
        if(is_null($movimento)){
            return null;
        }
        return $movimento->mapTo(MovimentoDto::class);
    }
    public function getMovimentoByNumeroControleSaida(int $numeroControleSaida): ?MovimentoDto
    {
        $movimento = Movimentos::from('movimentos as m')
            ->join('items as i', 'i.id', '=', 'm.item_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->select([
                'm.id as movimentacao_id',
                'i.id as item_id',
                'i.nome as nome_item',
                'i.estoque as quantidade_estoque',
                'm.tipo as tipo_movimentacao',
                'm.quantidade as quantidade_movimentada',
                'm.data_movimentacao',
                'm.nota_fiscal',
                'm.fornecedor',
                'm.numero_controle_saida',
                'm.local_destino',
                'u.name as usuario_responsavel',
                'm.criado_em',
                'm.atualizado_em',
                'm.atualizado_por',
            ])
            ->where('m.numero_controle_saida', '=', $numeroControleSaida)
            ->first();
        if(is_null($movimento)){
            return null;
        }
        return $movimento->mapTo(MovimentoDto::class);
    }
}
