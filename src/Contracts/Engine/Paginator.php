<?php


namespace DevelMe\RestfulList\Contracts\Engine;


interface Paginator
{
    public function paginate(Data $data, array $pagination);
}