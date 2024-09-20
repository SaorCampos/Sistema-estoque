<?php

namespace App\Data\Repositories\PerfilPermissao;

use App\Models\PerfilPermissao;
use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;

class PerfilPermissaoRepository implements IPerfilPermissaoRepository
{
    public function createPerfilPermissoes(string $perfilId, string $permissaoId): PerfilPermissao
    {
        return PerfilPermissao::create([
            'perfil_id' => $perfilId,
            'permissao_id' => $permissaoId
        ]);
    }
    public function deletePerfilPermissoes(string $perfilId, string $permissaoId): bool
    {
        return PerfilPermissao::where('perfil_id', $perfilId)->where('permissao_id', $permissaoId)->delete();
    }
}
