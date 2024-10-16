<?php

namespace App\Http\Requests\Item;

use App\Http\Requests\BaseRequest;
/**
 * @property string nome
 * @property string categoria
 * @property string criadoPor
 * @property string descricao
 */
class ItemsListingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'nome' => 'string',
            'categoria' => 'string',
            'criadoPor' => 'string',
            'descricao' => 'string',
        ];
    }
}
