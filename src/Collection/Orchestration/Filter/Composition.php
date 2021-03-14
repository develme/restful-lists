<?php


namespace DevelMe\RestfulList\Collection\Orchestration\Filter;


use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Collection\Filters\Equals;
use DevelMe\RestfulList\Collection\Filters\NotEquals;
use DevelMe\RestfulList\Collection\Filters\Contains;
use DevelMe\RestfulList\Collection\Filters\In;
use DevelMe\RestfulList\Collection\Filters\StartsWith;
use DevelMe\RestfulList\Collection\Filters\EndsWith;
use DevelMe\RestfulList\Collection\Filters\Between;
use DevelMe\RestfulList\Collection\Filters\LessThan;
use DevelMe\RestfulList\Collection\Filters\LessThanOrEqual;
use DevelMe\RestfulList\Collection\Filters\GreaterThan;
use DevelMe\RestfulList\Collection\Filters\GreaterThanOrEqual;

class Composition implements Defaults
{
    public function defaults(): Closure
    {
        return fn($compare): Filtration => match ($compare) {
            'contains' => new Contains,
            'not_equals' => new NotEquals,
            'starts_with' => new StartsWith,
            'ends_with' => new EndsWith,
            'in' => new In,
            'between' => new Between,
            'less_than' => new LessThan,
            'less_than_or_equal' => new LessThanOrEqual,
            'greater_than' => new GreaterThan,
            'greater_than_or_equal' => new GreaterThanOrEqual,
            default => new Equals
        };
    }
}