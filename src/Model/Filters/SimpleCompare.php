<?php


namespace DevelMe\RestfulList\Model\Filters;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

abstract class SimpleCompare implements Filtration
{
    abstract public function operator(): string;

    public function filter($data, Setting $setting)
    {
        $data->where($setting->field(), $this->operator(), $setting->value());

        return $data;
    }
}