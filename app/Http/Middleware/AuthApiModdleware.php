<?php

namespace App\Http\Middleware;

use App\Support\Models\BaseResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthApiModdleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return BaseResponse::builder()
                ->setMessage('Unauthorized')
                ->setStatusCode(Response::HTTP_UNAUTHORIZED)
                ->response();
        }
        return $next($request);
    }
}
