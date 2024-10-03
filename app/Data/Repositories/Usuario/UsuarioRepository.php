<?php

namespace App\Data\Repositories\Usuario;

use App\Core\ApplicationModels\Pagination;
use App\Core\ApplicationModels\PaginatedList;
use App\Core\Dtos\UsuarioDto;
use App\Core\Repositories\Usuario\IUsuarioRepository;
use App\Http\Requests\Usuario\UsuarioListingRequest;
use App\Models\User;

class UsuarioRepository implements IUsuarioRepository
{
    public function getUsuarios(UsuarioListingRequest $request, Pagination $pagination): PaginatedList
    {
        $query = User::from('users as u')
            ->join('perfil as p', 'p.id', '=', 'u.perfil_id')
            ->withTrashed()
            ->select([
                'u.id',
                'u.name as nome',
                'p.id as perfilId',
                'p.nome as perfilNome',
                'u.email',
                'u.criado_em',
                'u.atualizado_em',
                'u.criado_por',
                'u.atualizado_por',
                'u.deletado_em',
            ])
            ->where($this->getFilters($request))
            ->orderBy('u.id', 'desc')
            ->paginate($pagination->perPage, ['*'], 'page', $pagination->page);
        return PaginatedList::fromPaginatedQuery(
            query: $query,
            pagination: $pagination,
            dtoClass: UsuarioDto::class
        );
    }
    private function getFilters(UsuarioListingRequest $request): array
    {
        $filters = [];
        if(!is_null($request->nome ?? null)){
            $filters[] = ['u.name', 'ilike', '%' . $request->nome . '%'];
        }
        if(!is_null($request->usuarioId ?? null)){
            $filters[] = ['u.id', '=', $request->usuarioId];
        }
        return $filters;
    }

    public function getUsuarioById(string $id): ?UsuarioDto
    {
        $usuario = User::from('users as u')
            ->join('perfil as p', 'p.id', '=', 'u.perfil_id')
            ->withTrashed()
            ->select([
                'u.id',
                'u.name as nome',
                'p.id as perfilId',
                'p.nome as perfilNome',
                'u.email',
                'u.criado_em',
                'u.atualizado_em',
                'u.criado_por',
                'u.atualizado_por',
                'u.deletado_em',
            ])
            ->where('u.id', $id)
            ->first();
        if(is_null($usuario)){
            return null;
        }
        return $usuario->mapTo(UsuarioDto::class);
    }
    public function createUsuario(User $usuario): User
    {
        return User::query()->create($usuario->getAttributes());
    }
    public function updateUsuario(string $id, User $usuario): bool
    {
        return User::where('id', $id)->update($usuario->getAttributes());
    }
    public function deleteUsuario(string $id): bool
    {
        return User::where('id', $id)->delete();
    }
    public function getUsuarioByEmail(string $email): ?UsuarioDto
    {
        $usuario = User::from('users as u')
            ->join('perfil as p', 'p.id', '=', 'u.perfil_id')
            ->withTrashed()
            ->select([
                'u.id',
                'u.name as nome',
                'p.id as perfilId',
                'p.nome as perfilNome',
                'u.email',
                'u.criado_em',
                'u.atualizado_em',
                'u.criado_por',
                'u.atualizado_por',
                'u.deletado_em',
            ])
            ->where('u.email', $email)
            ->first();
        if(is_null($usuario)){
            return null;
        }
        return $usuario->mapTo(UsuarioDto::class);
    }
}
