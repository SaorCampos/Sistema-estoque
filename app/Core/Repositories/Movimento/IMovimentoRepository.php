<?php

namespace App\Core\Repositories\Movimento;

use App\Models\Movimentos;
use App\Core\Dtos\MovimentoDto;
use Illuminate\Support\Collection;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Http\Requests\Movimento\MovimentosListingRequest;

interface IMovimentoRepository
{
    public function getAllMovimetacoes(MovimentosListingRequest $request, Pagination $pagination): PaginatedList;
    public function createMovimentacao(Movimentos $movimento): Movimentos;
    public function getMovimentoById(string $id): MovimentoDto;
    public function getMovimentacoesByIdList(array $ids): Collection;
}
