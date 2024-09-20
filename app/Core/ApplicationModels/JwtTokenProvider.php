<?php

namespace App\Core\ApplicationModels;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Core\ApplicationModels\JwtToken;
use Illuminate\Http\Exceptions\HttpResponseException;

class JwtTokenProvider
{
    public function getJwtToken(): JwtToken
    {
        $user = JWTAuth::parseToken()->authenticate();
        $token = JWTAuth::getToken();
        $jwtToken = new JwtToken();
        $jwtToken->accessToken = $token;
        $jwtToken->perfilId = $user->perfil_id;
        $jwtToken->userName = $user->name;
        $jwtToken->permissoes = $user->perfil->permissoes->pluck('nome')->toArray();
        return $jwtToken;
    }
}
