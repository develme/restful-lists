<?php


namespace DevelMe\RestfulList\Model\Orchestration\Order;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orders\Arrangement as ArrangementInterface;
use DevelMe\RestfulList\Contracts\Orders\Setting;

class Arrangement implements ArrangementInterface
{
    public function arrange(Setting $setting, Data $data)
    {
        $data->data()->orderBy($setting->field(), $setting->direction());
    }
}