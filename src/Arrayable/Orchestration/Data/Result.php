<?php


namespace DevelMe\RestfulList\Arrayable\Orchestration\Data;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Engine\Result as ResultInterface;

class Result implements ResultInterface
{

    public function get(Data $data)
    {
        return $data->data();
    }
}