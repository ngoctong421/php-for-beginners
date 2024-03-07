<?php

class Paginator
{
    public $limit;
    public $offset;
    public $previous;
    public $next;
    public $total_pages;

    public function __construct($page, $records_per_page, $total_records)
    {
        $this->limit = $records_per_page;

        $page = filter_var($page, FILTER_VALIDATE_INT, [
            'options' => [
                'default'   => 1,
                'min_range' => 1,
             ],
         ]);

        if ($page > 1) {
            $this->previous = $page - 1;
        }

        $this->total_pages = ceil($total_records / $records_per_page);

        if ($page < $this->total_pages) {
            $this->next = $page + 1;
        }

        $this->offset = $records_per_page * ($page - 1);
    }
}
