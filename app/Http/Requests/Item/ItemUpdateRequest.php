<?php

namespace App\Http\Requests\Item;

use App\Http\Requests\BaseRequest;

/**
 * @property string $id
 * @property string $categoria
 * @property string $subCategoria
 * @property string $descricao
 */
class ItemUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id' => 'string|required',
            'categoria' => 'string',
            'subCategoria' => 'string',
            'descricao' => 'string',
        ];
    }
}
