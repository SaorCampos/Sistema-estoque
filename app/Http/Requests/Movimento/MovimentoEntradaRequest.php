<?php

namespace App\Http\Requests\Movimento;

use App\Http\Requests\BaseRequest;

/**
 * @property array $entradas
 */
class MovimentoEntradaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'entradas' => 'required|array|min:1',
            'entradas.*.itemId' => 'required|string|',
            'entradas.*.quantidade' => 'required|integer|min:1',
            'entradas.*.data' => 'required|date|date_format:Y-m-d',
            'entradas.*.notaFiscal' => 'required|integer',
            'entradas.*.fornecedor' => 'required|string',
        ];
    }
}
