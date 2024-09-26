<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\BaseRequest;

/**
 * @property string $nome
 * @property string $usuarioId
 */
class UsuarioListingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nome" => "string",
            "usuarioId" => "string",
        ];
    }
}
