<?php


namespace DevelMe\RestfulList\Collection\Filters;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class Between implements Filtration
{

    public function filter($data, Setting $setting)
    {
        $field = $setting->field();
        $from = $setting->value()['from'];
        $to = $setting->value()['to'];

        return $data->where($field, ">=", $from)->where($field, "<=", $to);
    }
}