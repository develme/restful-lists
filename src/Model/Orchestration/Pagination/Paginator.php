<?php


namespace DevelMe\RestfulList\Model\Orchestration\Pagination;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Pagination\Setting;

class Paginator implements \DevelMe\RestfulList\Contracts\Pagination\Paginator
{

    public function paginate(Setting $setting, Data $data)
    {
        $data = $data->data();

        $data->skip($setting->start())->limit($setting->end());
    }
}