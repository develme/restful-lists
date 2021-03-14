<?php


namespace DevelMe\RestfulList\Collection\Orchestration\Order;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orders\Arrangement as ArrangementInterface;
use DevelMe\RestfulList\Contracts\Orders\Setting;

class Arrangement implements ArrangementInterface
{
    public function arrange(Setting $setting, Data $data)
    {
        $data->setData($data->data()->sortBy($setting->field(), SORT_REGULAR, $setting->direction() === 'desc'));
    }
}