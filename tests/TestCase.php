<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $jsonStructureBase = [
        'message',
        'data',
        'details',
        'statusCode',
    ];

    /**
     * Set the currently logged in user for the application.
     *
     * @param Authenticatable $user
     * @param string|null $driver
     * @return $this
     */
    public function actingAs($user, $driver = null): static
    {
        if ($driver === 'jwt') {
            $token = JWTAuth::fromUser($user);
            $this->withHeader('Authorization', 'Bearer ' . $token);
            parent::actingAs($user);
        } else {
            parent::actingAs($user, $driver);
        }

        return $this;
    }
}
