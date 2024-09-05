<?php

namespace App\Data\Services;
use Closure;
use Illuminate\Support\Facades\DB;


class DbTransaction implements IDbTransaction 
{
    public function run(Closure $closure)
    {
        return DB::transaction($closure);
    }
}
