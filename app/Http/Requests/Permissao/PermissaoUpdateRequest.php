<?php

namespace App\Http\Requests\Permissao;

use App\Http\Requests\BaseRequest;

/**
 * @property array $permissoesId
 */
class PermissaoUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "permissoesId" => "array|required",
            "permissoesId.*" => "string|required"
        ];
    }
}
