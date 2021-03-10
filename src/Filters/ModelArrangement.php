<?php


namespace DevelMe\RestfulList\Filters;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orders\Arrangement;
use DevelMe\RestfulList\Contracts\Orders\Setting;

class ModelArrangement implements Arrangement
{

    public function arrange(Setting $setting, Data $data)
    {
        return $data->data()->orderBy($setting->field(), $setting->direction());
    }
}