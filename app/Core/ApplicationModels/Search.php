<?php

namespace App\Core\ApplicationModels;

use App\Core\Traits\AutoMapper;
use Exception;
use Illuminate\Http\Request;



class Search
{
    use AutoMapper;
    private string $search;

    public static function create(string $search): Search
    {
        return (new Search())->mapFromArray(['search' => $search]);
    }

    public static function createFromRequest(Request $request): ?static
    {
        try {
            $instance = new self();
            return $instance->mapFromArray($request->all());
        } catch (Exception $e) {
            return null;
        }
    }

    public function __toString(): string
    {
        return '%' . $this->search . '%';
    }

    public function value(): string
    {
        return $this->search;
    }
}
