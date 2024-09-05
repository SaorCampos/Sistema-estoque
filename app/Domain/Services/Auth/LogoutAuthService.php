<?php

namespace App\Domain\Services\Auth;

use App\Core\Repositories\Auth\IAuthRepository;
use App\Core\Services\Auth\ILogoutAuthService;

class LogoutAuthService implements ILogoutAuthService
{
    public function __construct(private IAuthRepository $authRepository)
    {}

    public function logout(): void
    {
        try {
            $this->authRepository->logout();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
