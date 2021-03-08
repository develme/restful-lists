<?php


namespace DevelMe\RestfulList\Contracts\Comparator;


use Closure;

interface Registration
{
    public function register(?Closure $compositions): void;
}