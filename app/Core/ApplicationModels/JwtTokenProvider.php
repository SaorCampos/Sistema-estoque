<?php

namespace App\Core\ApplicationModels;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Core\ApplicationModels\JwtToken;
use Illuminate\Http\Exceptions\HttpResponseException;

class JwtTokenProvider
{
    private JwtToken $jwtToken;

    public function __construct()
    {
        if(!auth()->user()){
            throw new HttpResponseException(response()->json(['error' => 'Token inválido'], 401));
        }
        $user = JWTAuth::parseToken()->authenticate();
        $token = JWTAuth::getToken();
        $jwtToken = new JwtToken();
        $jwtToken->accessToken = (string) $token;
        $jwtToken->perfilId = $user->perfil_id;
        $jwtToken->userName = $user->name;
        $jwtToken->permissoes = $user->perfil->permissoes->pluck('nome')->toArray();
        $this->jwtToken = $jwtToken;
    }

    public function getJwtToken(): JwtToken
    {
        return $this->jwtToken;
    }

    public function validateRole(string $permissao): void
    {
        $this->jwtToken->validateRole($permissao);
    }
}
