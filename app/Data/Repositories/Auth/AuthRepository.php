<?php

namespace App\Data\Repositories\Auth;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Core\ApplicationModels\JwtToken;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Core\Repositories\Auth\IAuthRepository;

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
        $usuario->load('perfil.permissoes');
        $jwtToken = new JwtToken();
        $jwtToken->accessToken = $token;
        $jwtToken->perfilId = $usuario->perfil_id;
        $jwtToken->userName = $usuario->name;
        $jwtToken->permissoes = $usuario->perfil->permissoes->pluck('nome')->toArray();
        return $jwtToken;
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
