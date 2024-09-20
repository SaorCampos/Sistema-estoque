<?php

namespace App\Http\Requests\Permissao;

use App\Http\Requests\BaseRequest;

/**
 * @property string $nome
 * @property string $permissaoId
 */
class PermissaoListingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nome" => "string",
            "permissaoId" => "string",
        ];
    }
}
