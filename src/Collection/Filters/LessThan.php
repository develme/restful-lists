<?php


namespace DevelMe\RestfulList\Collection\Filters;

use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class LessThan implements Filtration
{

    public function filter($data, Setting $setting)
    {
        return $data->where($setting->field(), "<", $setting->value());
    }
}