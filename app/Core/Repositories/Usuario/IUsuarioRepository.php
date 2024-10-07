<?php

namespace App\Core\Repositories\Usuario;

use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\Pagination;
use App\Core\Dtos\UsuarioDto;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Models\User;
use Illuminate\Support\Collection;

interface IUsuarioRepository
{
    public function getUsuarios(UsuarioListingRequest $request, Pagination $pagination): PaginatedList;
    public function getUsuarioById(string $id): ?UsuarioDto;
    public function createUsuario(User $usuario): User;
    public function updateUsuario(string $id, User $usuario): bool;
    public function deleteUsuario(string $id): bool;
    public function getUsuarioByEmail(string $email): ?UsuarioDto;
    public function getUsuariosByIdList(array $ids): Collection;
    public function reativarUsuario(string $id): bool;
}
