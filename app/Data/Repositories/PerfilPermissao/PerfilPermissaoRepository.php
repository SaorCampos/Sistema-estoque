<?php

namespace App\Data\Repositories\PerfilPermissao;

use App\Core\Repositories\PerfilPermissao\IPerfilPermissaoRepository;
use App\Models\PerfilPerimissao;

class PerfilPermissaoRepository implements IPerfilPermissaoRepository
{
    public function createPerfilPermissoes(string $perfilId, string $permissaoId): PerfilPerimissao
    {
        return PerfilPerimissao::create([
            'perfil_id' => $perfilId,
            'permissao_id' => $permissaoId
        ]);
    }
    public function deletePerfilPermissoes(string $perfilId, string $permissaoId): bool
    {
        return PerfilPerimissao::where('perfil_id', $perfilId)->where('permissao_id', $permissaoId)->delete();
    }
}
