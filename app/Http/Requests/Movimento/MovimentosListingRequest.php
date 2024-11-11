<?php

namespace App\Http\Requests\Movimento;

use App\Http\Requests\BaseRequest;

/**
 * @property string $tipoMovimentacao
 * @property string $nomeItem
 * @property string $itemId
 * @property string $dataMovimentacao
 * @property int $notaFiscal
 * @property string $fornecedor
 * @property string $localDestino
 * @property string $usuarioResponsavel
 * @property string $dataInicial
 * @property string $dataFinal
 */
class MovimentosListingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'tipoMovimentacao' => 'string|in:ENTRADA,SAIDA',
            'nomeItem' => 'string',
            'itemId' => 'string',
            'dataMovimentacao' => 'date',
            'notaFiscal' => 'int|min:1',
            'fornecedor' => 'string',
            'localDestino' => 'string',
            'usuarioResponsavel' => 'string',
            'dataInicial' => 'date|date_format:Y-m-d',
            'dataFinal' => 'date|date_format:Y-m-d',
        ];
    }
}
