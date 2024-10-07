<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\BaseRequest;
/**
 * @property array $usuariosId
 */
class UsuarioDeleteRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "usuariosId" => "array|required",
            "usuariosId.*" => "string|required"
        ];
    }
}
