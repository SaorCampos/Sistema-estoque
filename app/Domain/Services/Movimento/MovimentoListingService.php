<?php

namespace App\Domain\Services\Movimento;

use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Repositories\Movimento\IMovimentoRepository;
use App\Core\Services\Movimento\IMovimentoListingService;
use App\Http\Requests\Movimento\MovimentosListingRequest;

class MovimentoListingService implements IMovimentoListingService
{
    public function __construct(
        private IMovimentoRepository $movimentoRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function getAllMovimetacoes(MovimentosListingRequest $request, Pagination $pagination): PaginatedList
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Listar Movimentações');
        return $this->movimentoRepository->getAllMovimetacoes($request, $pagination);
    }
}
