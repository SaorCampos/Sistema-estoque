<?php

namespace App\Core\ApplicationModels;

use App\Core\Traits\ArraySerializer;

class JwtToken implements IArraySerializer
{
    use ArraySerializer;
    public string $accessToken = '';
    public string $expiresIn = '';
    public string $userName = '';
}
