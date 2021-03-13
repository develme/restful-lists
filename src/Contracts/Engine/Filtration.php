<?php


namespace DevelMe\RestfulList\Contracts\Engine;


interface Filtration
{
    public function filter(Data $data, array $filters);
}