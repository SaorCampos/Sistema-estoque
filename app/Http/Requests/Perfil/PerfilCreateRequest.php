<?php

namespace App\Http\Requests\Perfil;

use App\Http\Requests\BaseRequest;
/**
 * @property string $nome
 * @property array $permissoesId
 */
class PerfilCreateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nome" => "string|required",
            "permissoesId" => "array|required",
            "permissoesId.*" => "string|required"
        ];
    }
}
