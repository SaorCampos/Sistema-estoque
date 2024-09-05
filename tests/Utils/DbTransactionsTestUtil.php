<?php

namespace Tests\Utils;

use App\Data\Services\IDbTransaction;
use Closure;

class DbTransactionsTestUtil implements IDbTransaction
{
    private int $executed;
    
    public function __construct()
    {
        $this->executed = 0;
    }
    
    public function run(Closure $closure): mixed
    {
        $this->executed++;
        return $closure();
    }

    public function executed(): int
    {
        return $this->executed;
    }

}
