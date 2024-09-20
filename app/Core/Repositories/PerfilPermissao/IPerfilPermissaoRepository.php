<?php

namespace App\Core\Repositories\PerfilPermissao;

use App\Models\PerfilPermissao;

interface IPerfilPermissaoRepository
{
    public function createPerfilPermissoes(string $perfilId, string $permissaoId): PerfilPermissao;
    public function deletePerfilPermissoes(string $perfilId, string $permissaoId): bool;
}
