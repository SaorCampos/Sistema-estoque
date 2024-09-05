<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

/**
 * @property string $email
 * @property string $password
 */
class LoginAuthRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "required|email",
            "password" => "required",
        ];
    }
}
