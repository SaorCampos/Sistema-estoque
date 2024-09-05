<?php

namespace App\Core\Services\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Http\Requests\Auth\LoginAuthRequest;

interface ILoginAuthService
{
    public function login(LoginAuthRequest $request): JwtToken;
}
