<?php


namespace DevelMe\RestfulList\Contracts\Filters;


interface Setting
{
    public function field(): string;

    public function type(): string;

    public function value(): mixed;
}