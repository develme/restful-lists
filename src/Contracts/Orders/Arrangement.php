<?php


namespace DevelMe\RestfulList\Contracts\Orders;


use DevelMe\RestfulList\Contracts\Engine\Data;

interface Arrangement
{
    public function arrange(Setting $setting, Data $data);
}