<?php

namespace App\Core\Services\Permissao;

use App\Http\Requests\Permissao\PermissaoUpdateRequest;
use Illuminate\Support\Collection;

interface IPermissaoUpdateService
{
    public function ativarPermissao(PermissaoUpdateRequest $request): Collection;
}
