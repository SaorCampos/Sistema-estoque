<?php

namespace App\Core\Services\Permissao;

use Illuminate\Support\Collection;
use App\Http\Requests\Permissao\PermissaoUpdateRequest;

interface IPermissaoDeleteService
{
    public function desativarPermissao(PermissaoUpdateRequest $request): Collection;
}
