<?php


namespace DevelMe\RestfulList\Collection\Filters;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class Contains implements Filtration
{

    public function filter($data, Setting $setting)
    {
        return $data->filter(fn($row) => str_contains($row[$setting->field()], $setting->value()));
    }
}