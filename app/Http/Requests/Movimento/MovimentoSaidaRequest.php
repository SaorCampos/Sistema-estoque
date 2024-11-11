<?php

namespace App\Http\Requests\Movimento;

use App\Http\Requests\BaseRequest;
/**
 * @property array saidas
 */
class MovimentoSaidaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'saidas' => 'required|array|min:1',
            'saidas.*.itemId' => 'required|string',
            'saidas.*.quantidade' => 'required|integer|min:1',
            'saidas.*.data' => 'required|date|date_format:Y-m-d',
            'saidas.*.numeroControleSaida' => 'required|integer|min:1',
            'saidas.*.localDestino' => 'required|string|max:255',
        ];
    }
}
