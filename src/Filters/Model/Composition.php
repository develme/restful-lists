<?php


namespace DevelMe\RestfulList\Filters\Model;


use Closure;
use DevelMe\RestfulList\Contracts\Comparator\Defaults;
use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Filters\Model\Contains;
use DevelMe\RestfulList\Filters\Model\Equals;
use DevelMe\RestfulList\Filters\Model\In;
use DevelMe\RestfulList\Filters\Model\NotEquals;
use DevelMe\RestfulList\Filters\Model\StartsWith;
use DevelMe\RestfulList\Filters\Model\EndsWith;
use DevelMe\RestfulList\Filters\Model\Between;
use DevelMe\RestfulList\Filters\Model\LessThan;
use DevelMe\RestfulList\Filters\Model\LessThanOrEqual;
use DevelMe\RestfulList\Filters\Model\GreaterThan;
use DevelMe\RestfulList\Filters\Model\GreaterThanOrEqual;

class Map implements Defaults
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