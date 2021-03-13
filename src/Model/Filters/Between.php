<?php


namespace DevelMe\RestfulList\Model\Filters;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class Between implements Filtration
{

    public function filter($data, Setting $setting)
    {
        $field = $setting->field();
        $from = $setting->value()['from'];
        $to = $setting->value()['to'];

        $data->where($field, '>=', $from ?? null);
        $data->where($field, "<=", $to ?? null);

        return $data;
    }
}