<?php

namespace App\Data\Repositories\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Models\User;

class AuthRepository implements IAuthRepository
{
    public function login(LoginAuthRequest $request): ?JwtToken
    {
        $token = auth()->attempt([
            'name' => $request->name,
            'password' => $request->password
        ]);
        if (!$token) return null;
        $usuario = User::where('name', $request->name)->first();
        $jwtToken = new JwtToken();
        $jwtToken->accessToken = $token;
        $jwtToken->perfilId = $usuario->perfil_id;
        return $jwtToken;
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
