<?php

namespace App\Domain\Services\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Core\Services\Auth\ILoginAuthService;
use App\Http\Requests\Auth\LoginAuthRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginAuthService implements ILoginAuthService
{
    public function __construct(private IAuthRepository $authRepository)
    {}

    public function login(LoginAuthRequest $request): JwtToken
    {
        $jwtToken = $this->authRepository->login($request);
        if (!$jwtToken) throw new \Exception('Invalid credentials', 401);
        return $jwtToken;
    }
}
