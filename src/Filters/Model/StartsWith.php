<?php


namespace DevelMe\RestfulList\Filters\Model;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class StartsWith implements Filtration
{

    public function filter($data, Setting $setting)
    {
        $data->where($setting->field(), 'like', $this->prepValue((string) $setting->value()));

        return $data;
    }

    private function prepValue(string $value): string
    {
        return "%$value";
    }
}