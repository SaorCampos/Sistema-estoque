<?php

namespace App\Domain\Services\Permissao;

use App\Core\ApplicationModels\JwtTokenProvider;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Repositories\Permissao\IPermissaoRepository;
use App\Core\Services\Permissao\IPermissaoListingService;
use App\Http\Requests\Permissao\PermissaoListingRequest;

class PermissaoListingService implements IPermissaoListingService
{
    public function __construct(
        private IPermissaoRepository $permissaoRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function getPermissoes(PermissaoListingRequest $request, Pagination $pagination): PaginatedList
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Listar Permissões');
        return $this->permissaoRepository->getPermissoes($request, $pagination);
    }
}
