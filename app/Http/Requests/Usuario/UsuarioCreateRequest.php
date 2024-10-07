<?php

namespace App\Http\Requests\Usuario;

use App\Http\Requests\BaseRequest;

/**
 *  @property string $nome
 *  @property string $email
 *  @property string $senha
 *  @property string $perfilId
 */
class UsuarioCreateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nome" => "string|required",
            "email" => "string|required|email",
            "senha" => "string|required",
            "perfilId" => "string|required",
        ];
    }
}
