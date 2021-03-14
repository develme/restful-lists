<?php

namespace DevelMe\RestfulList\Collection\Orchestration;

use Closure;
use DevelMe\RestfulList\Collection\Orchestration\Data\Counter;
use DevelMe\RestfulList\Collection\Orchestration\Data\Result;
use DevelMe\RestfulList\Collection\Orchestration\Filter\Composer;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Engines\Facades\Filtration;
use DevelMe\RestfulList\Engines\Facades\Arrangement as ArrangementFacade;
use DevelMe\RestfulList\Collection\Orchestration\Order\Arrangement;

class Composition implements Defaults
{
    public function defaults(): Closure
    {
        $composer = new Composer;
        $composer->register();

        return fn($compare) => match ($compare) {
            'filter' => new Filtration($composer),
            'counter' => new Counter,
            'result' => new Result,
            'order' => new ArrangementFacade(new Arrangement),
            default => throw new \Exception("Unable to handle: $compare")
        };
    }
}