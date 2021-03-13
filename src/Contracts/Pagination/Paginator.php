<?php


namespace DevelMe\RestfulList\Contracts\Pagination;

use DevelMe\RestfulList\Contracts\Engine\Data;

interface Paginator
{
    public function paginate(Setting $setting, Data $data);
}