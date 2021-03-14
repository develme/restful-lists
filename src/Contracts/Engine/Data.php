<?php


namespace DevelMe\RestfulList\Contracts\Engine;


interface Data
{
    /**
     * Request the data the engine is using
     */
    public function data();

    public function setData($data);
}