<?php

namespace App\Data\Services;
use Closure;


interface IDbTransaction
{
    public function run(Closure $closure);
}
