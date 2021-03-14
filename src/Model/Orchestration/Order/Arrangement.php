<?php


namespace DevelMe\RestfulList\Model\Orchestration\Order;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orders\Arrangement as ArrangementContract;
use DevelMe\RestfulList\Contracts\Orders\Setting;

class Arrangement implements ArrangementContract
{

    public function arrange(Setting $setting, Data $data)
    {
        $data->data()->orderBy($setting->field(), $setting->direction());
    }
}