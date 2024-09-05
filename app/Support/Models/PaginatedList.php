<?php


namespace App\Support\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class PaginatedList
 * @package App\Support\Models
 */
class PaginatedList extends Model
{
    /**
     * @var Collection
     */
    public Collection $list;
    /**
     * @var Pagination | array
     */
    public Pagination | array $pagination;

    /**
     * @return static
     */
    public static function builder(): static
    {
        return new PaginatedList();
    }

    public static function builderByEloquentPagination(LengthAwarePaginator $paginator, Pagination $pagination): static
    {
        $pagination
            ->setLastPage($paginator->lastPage())
            ->setTotal($paginator->total());
        return PaginatedList::builder()
            ->setList(collect($paginator->items()))
            ->setPagination($pagination);
    }

    /**
     * @param Collection $list
     * @return PaginatedList
     */
    public function setList(Collection $list): PaginatedList
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @param Pagination $pagination
     * @return PaginatedList
     */
    public function setPagination(Pagination $pagination): PaginatedList
    {
        $this->pagination = $pagination;
        return $this;
    }
}
