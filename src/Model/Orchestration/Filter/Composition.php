<?php


namespace DevelMe\RestfulList\Model\Orchestration\Filter;


use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Model\Filters\Contains;
use DevelMe\RestfulList\Model\Filters\Equals;
use DevelMe\RestfulList\Model\Filters\In;
use DevelMe\RestfulList\Model\Filters\NotEquals;
use DevelMe\RestfulList\Model\Filters\StartsWith;
use DevelMe\RestfulList\Model\Filters\EndsWith;
use DevelMe\RestfulList\Model\Filters\Between;
use DevelMe\RestfulList\Model\Filters\LessThan;
use DevelMe\RestfulList\Model\Filters\LessThanOrEqual;
use DevelMe\RestfulList\Model\Filters\GreaterThan;
use DevelMe\RestfulList\Model\Filters\GreaterThanOrEqual;

class Composition implements Defaults
{
    /**
     * @return Closure
     */
    public function defaults(): Closure
    {
        return fn($compare): Filtration => match($compare) {
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