<?php


namespace App\Support\Models;

class Pagination extends Model
{
    public int $total;
    public int $perPage;
    public int $currentPage;
    public int $lastPage;
    public int $previousPage;
    public int $nextPage;

    public static function builder(): static
    {
        return new Pagination();
    }

    public function setTotal(int $total): Pagination
    {
        $this->total = $total;
        $this->previousPage = $this->currentPage > 1 ? $this->currentPage - 1 : -1;
        $this->nextPage = $this->currentPage === $this->lastPage ? -1 : $this->currentPage + 1;
        return $this;
    }

    public function setLastPage(int $lastPage): Pagination
    {
        $this->lastPage = $lastPage;
        return $this;
    }

    public function setPreviousPage(int $previousPage): Pagination
    {
        $this->previousPage = $previousPage;
        return $this;
    }

    public function setPerPage(int $perPage): Pagination
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function setCurrentPage(int $currentPage): Pagination
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function offset(): int
    {
        return $this->perPage * ($this->currentPage - 1);
    }

    public function limit(): int
    {
        return $this->perPage;
    }
}
