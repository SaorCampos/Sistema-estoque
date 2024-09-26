<?php

namespace App\Core\Repositories\Usuario;

use App\Core\ApplicationModels\PaginatedList;
use App\Core\ApplicationModels\Pagination;
use App\Core\Dtos\UsuarioDto;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Models\User;

interface IUsuarioRepository
{
    public function getUsuarios(UsuarioListingRequest $request, Pagination $pagination): PaginatedList;
    public function getUsuario(string $id): ?UsuarioDto;
    public function createUsuario(User $usuario): User;
    public function updateUsuario(string $id, User $usuario): bool;
    public function deleteUsuario(string $id): bool;
}
