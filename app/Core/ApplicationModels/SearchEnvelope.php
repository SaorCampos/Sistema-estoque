<?php

namespace App\Core\ApplicationModels;
use DateTime;


class SearchEnvelope
{
    public ?Pagination $pagination = null;
    public ?Search $search = null;

    public function __construct()
    {
        $request = request()->all();
        if (isset($request['page']) && isset($request['perPage'])) {
            $this->pagination = new Pagination($request['page'], $request['perPage']); 
        }
        if (isset($request['search'])) {
            $this->search = new Search($request['search']);
        }
    }   
}