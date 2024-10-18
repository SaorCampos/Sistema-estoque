<?php

namespace App\Http\Requests\Item;

use App\Http\Requests\BaseRequest;

/**
 * @property string $nome
 * @property string $categoria
 * @property string $subCategoria
 * @property string $descricao
 * @property int $quantidade
 */
class ItemCreateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'nome' => 'string|required',
            'categoria' => 'string|required',
            'subCategoria' => 'string',
            'descricao' => 'string',
            'quantidade' => 'integer|required|min:1',
        ];
    }
}
