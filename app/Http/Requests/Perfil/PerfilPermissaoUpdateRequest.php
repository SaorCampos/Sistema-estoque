<?php

namespace App\Http\Requests\Perfil;

use App\Http\Requests\BaseRequest;

/**
 * @property string $perfilId
 * @property array $permissoesId
 */
class PerfilPermissaoUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "perfilId" => "string|required",
            "permissoesId" => "array|required",
            "permissoesId.*" => "string|required"
        ];
    }
}
