<?php

namespace App\Core\ApplicationModels;
use App\Core\Traits\AutoMapper;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Nette\ArgumentOutOfRangeException;
use Exception;

class Pagination
{
    use AutoMapper;
    public int $page;
    public int $perPage;
    public ?int $total;

    public static function createFromRequest(Request $request): ?static
    {
        $array = $request->all();
        $array['total'] = null;
        $pagination = new Pagination;
        try {
            $pagination = $pagination->mapFromArray($array);
            $pagination->validate();
            return $pagination;
        } catch(Exception $e) {
            return null;
        }
    }

    private function validate()
    {
        if ($this->page <= 0) {
            throw new InvalidArgumentException('Page must to be positive.');
        }
        if ($this->perPage <= 0) {
            throw new InvalidArgumentException('PerPage must to be positive.');
        }
        if ($this->perPage > 1000) {
            throw new ArgumentOutOfRangeException('PerPage must to be between 1 and 1000.');
        }
    }
}
