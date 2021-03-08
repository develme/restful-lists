<?php


namespace DevelMe\RestfulList\Filters\Model;

use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class In implements Filtration
{

    public function filter($data, Setting $setting)
    {
        $data->whereIn($setting->field(), $setting->value());

        return $data;
    }
}