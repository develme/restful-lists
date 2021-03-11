<?php


namespace DevelMe\RestfulList\Contracts;


use Closure;

interface Registration
{
    public function register(?Closure $compositions): void;
}