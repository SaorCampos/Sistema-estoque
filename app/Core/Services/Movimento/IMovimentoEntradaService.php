<?php

namespace App\Core\Services\Movimento;

use Illuminate\Support\Collection;
use App\Http\Requests\Movimento\MovimentoEntradaRequest;

interface IMovimentoEntradaService
{
    public function createMovimentacaoEntrada(MovimentoEntradaRequest $request): Collection;
}
