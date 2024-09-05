<?php

namespace App\Domain\Services\Auth;

use App\Core\ApplicationModels\JwtToken;
use App\Core\Repositories\Auth\IAuthRepository;
use App\Core\Services\Auth\ILoginAuthService;
use App\Http\Requests\Auth\LoginAuthRequest;

class LoginAuthService implements ILoginAuthService
{
    public function __construct(private IAuthRepository $authRepository)
    {}

    public function login(LoginAuthRequest $request): JwtToken
    {
        $jwtToken = $this->authRepository->login($request);
        if (!$jwtToken) throw new \Exception('Invalid credentials', 401);
        $jwtToken->userName = auth()->user()->name;
        return $jwtToken;
    }
}
