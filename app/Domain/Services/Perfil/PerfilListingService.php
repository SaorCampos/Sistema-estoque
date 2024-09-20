<?php

namespace App\Domain\Services\Perfil;

use App\Core\Dtos\PerfilDetalhesDto;
use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\JwtTokenProvider;
use App\Http\Requests\Perfil\PerfilListingRequest;
use App\Core\Repositories\Perfil\IPerfilRepository;
use App\Core\Services\Perfil\IPerfilListingService;
use Illuminate\Http\Exceptions\HttpResponseException;

class PerfilListingService implements IPerfilListingService
{

    public function __construct(
        private IPerfilRepository $perfilRepository,
        private JwtTokenProvider $jwtTokenProvider,
    )
    {
    }

    public function getPerfis(PerfilListingRequest $request, Pagination $pagination): PaginatedList
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Listar Perfis');
        return $this->perfilRepository->getPerfis($request, $pagination);
    }

    public function getPermissoesByPerfilId(string $perfilId): PerfilDetalhesDto
    {
        $jwtToken = $this->jwtTokenProvider->getJwtToken();
        $jwtToken->validateRole('Listar Perfis');
        $perfilDto = $this->perfilRepository->getPerfilById($perfilId);
        if(!$perfilDto){
            throw new HttpResponseException(response()->json(['message' => 'Perfil não encontrado.'], 404));
        }
        $permissoes = $this->perfilRepository->getPermissoesByPerfilId($perfilId);
        $perfilDetalhesDto = new PerfilDetalhesDto($perfilDto, $permissoes);
        return $perfilDetalhesDto;
    }
}
