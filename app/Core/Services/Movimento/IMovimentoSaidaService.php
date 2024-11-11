<?php

namespace App\Core\Services\Movimento;

use App\Http\Requests\Movimento\MovimentoSaidaRequest;
use Illuminate\Support\Collection;

interface IMovimentoSaidaService
{
    public function createMovimentacaoSaida(MovimentoSaidaRequest $request): Collection;
}
