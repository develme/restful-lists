<?php


namespace DevelMe\RestfulList\Contracts\Engine;


interface Arrangement
{
    public function arrange(Data $data, array $orders);
}