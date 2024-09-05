<?php

namespace App\Core\Repositories\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Http\Requests\Auth\LoginAuthRequest;

interface IAuthRepository
{
    public function login(LoginAuthRequest $request): ?JwtToken;
    public function logout(): void;
}
