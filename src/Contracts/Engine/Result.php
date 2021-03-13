<?php


namespace DevelMe\RestfulList\Contracts\Engine;

interface Result
{
    public function get(Data $data);
}