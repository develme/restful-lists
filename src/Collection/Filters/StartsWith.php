<?php


namespace DevelMe\RestfulList\Collection\Filters;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class StartsWith implements Filtration
{

    public function filter($data, Setting $setting)
    {
        return $data->filter(fn($row) => str_starts_with($row[$setting->field()], $setting->value()));
    }
}