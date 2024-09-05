<?php

namespace App\Http\Controllers;

use App\Core\Services\Auth\ILoginAuthService;
use App\Core\Services\Auth\ILogoutAuthService;
use App\Http\Requests\Auth\LoginAuthRequest;
use App\Support\Models\BaseResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private ILoginAuthService $loginAuthService;
    private ILogoutAuthService $logoutAuthService;

    public function __construct(
        ILoginAuthService $loginAuthService,
        ILogoutAuthService $logoutAuthService
    ) {
        $this->loginAuthService = $loginAuthService;
        $this->logoutAuthService = $logoutAuthService;
    }

    public function login(LoginAuthRequest $request): Response
    {
        try {
            $jwtToken = $this->loginAuthService->login($request);
            return BaseResponse::builder()
                ->setData($jwtToken)
                ->setMessage('Login successful')
                ->response();
        } catch (\Exception $e) {
            return BaseResponse::builder()
                ->setMessage($e->getMessage())
                ->setStatusCode($e->getCode())
                ->response();
        }
    }

    public function logout(): Response
    {
        try {
            $this->logoutAuthService->logout();
            return BaseResponse::builder()
                ->setMessage('Logout successful')
                ->response();
        } catch (\Exception $e) {
            return BaseResponse::builder()
                ->setMessage($e->getMessage())
                ->setStatusCode($e->getCode())
                ->response();
        }
    }
}
