<?php


namespace DevelMe\RestfulList\Arrayable\Orchestration;

use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Arrayable\Orchestration\Data\Counter;
use DevelMe\RestfulList\Engines\Facades\Arrangement as ArrangementFacade;
use DevelMe\RestfulList\Arrayable\Orchestration\Order\Arrangement;
use DevelMe\RestfulList\Arrayable\Orchestration\Data\Result;

class Composition implements Defaults
{
    public function defaults(): Closure
    {
        return fn($compare) => match ($compare) {
            'result' => new Result,
            'order' => new ArrangementFacade(new Arrangement),
            'counter' => new Counter,
            default => throw new \Exception("Unable to handle: $compare")
        };
    }
}