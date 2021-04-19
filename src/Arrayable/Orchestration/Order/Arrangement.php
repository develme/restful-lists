<?php


namespace DevelMe\RestfulList\Arrayable\Orchestration\Order;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orders\Arrangement as ArrangementInterface;
use DevelMe\RestfulList\Contracts\Orders\Setting;

class Arrangement implements ArrangementInterface
{

    public function arrange(Setting $setting, Data $data)
    {
        $original = $data->data();
        $values = [];

        foreach ($data->data() as $key => $value) {
            $values[$key] = $value[$setting->field()];
        }

        $setting->direction() === 'desc' ? arsort($values) : asort($values);

        $sorted = [];

        foreach (array_keys($values) as $key) {
            $sorted[$key] = $original[$key];
        }

        $data->setData(array_values($sorted));
    }
}