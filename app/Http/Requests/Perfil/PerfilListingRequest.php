<?php

namespace App\Http\Requests\Perfil;

use App\Http\Requests\BaseRequest;

/**
 * @property string $nome
 * @property string $perfilId
 */
class PerfilListingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "nome" => "string",
            "perfilId" => "string",
        ];
    }
}
