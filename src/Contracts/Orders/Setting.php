<?php


namespace DevelMe\RestfulList\Contracts\Orders;


interface Setting
{
    public function field(): string;

    public function direction(): string;
}