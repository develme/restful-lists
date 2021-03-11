<?php


namespace DevelMe\RestfulList\Contracts;


interface Orchestration
{
    public function orchestrate(string $ask): mixed;
}