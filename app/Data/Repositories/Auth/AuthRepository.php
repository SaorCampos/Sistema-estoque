<?php

namespace App\Data\Repositories\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Http\Requests\Auth\LoginAuthRequest;

class AuthRepository implements IAuthRepository
{
    public function login(LoginAuthRequest $request): ?JwtToken
    {
        $token = auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);
        if (!$token) return null;
        $jwtToken = new JwtToken();
        $jwtToken->accessToken = $token;
        return $jwtToken;
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
