<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\BaseRequest;

/**
 * @property string $email
 * @property string $novaSenha
 */
class UsuarioAlterarSenhaRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "string|required|email",
            "novaSenha" => "string|required",
        ];
    }
}
