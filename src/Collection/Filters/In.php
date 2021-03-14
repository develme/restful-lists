<?php


namespace DevelMe\RestfulList\Collection\Filters;

use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class In implements Filtration
{

    public function filter($data, Setting $setting)
    {
        return $data->whereIn($setting->field(), $setting->value());
    }
}