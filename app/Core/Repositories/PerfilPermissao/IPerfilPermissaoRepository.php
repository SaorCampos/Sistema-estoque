<?php

namespace App\Core\Repositories\PerfilPermissao;

use App\Models\PerfilPerimissao;

interface IPerfilPermissaoRepository
{
    public function createPerfilPermissoes(string $perfilId, string $permissaoId): PerfilPerimissao;
    public function deletePerfilPermissoes(string $perfilId, string $permissaoId): bool;
}
