<?php


namespace DevelMe\RestfulList\Model\Orchestration\Data;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Engine\Result as ResultContract;

class Result implements ResultContract
{

    public function get(Data $data)
    {
        return $data->data()->get();
    }
}