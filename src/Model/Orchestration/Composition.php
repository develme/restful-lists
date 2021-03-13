<?php


namespace DevelMe\RestfulList\Model\Orchestration;


use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Model\Orchestration\Count\Counter;
use DevelMe\RestfulList\Model\Orchestration\Engine\Filtration;
use DevelMe\RestfulList\Model\Orchestration\Filter\Composer;
use DevelMe\RestfulList\Model\Orchestration\Engine\Arrangement;
use DevelMe\RestfulList\Model\Orchestration\Order\Arrangement as SingleArrangement;
use DevelMe\RestfulList\Model\Orchestration\Pagination\Paginator;

class Composition implements Defaults
{
    /**
     * @return Closure
     */
    public function defaults(): Closure
    {
        $composer = new Composer;
        $composer->register();

        return fn($compare) => match($compare) {
            'filter' => new Filtration($composer),
            'order' => new Arrangement(new SingleArrangement()),
            'pagination' => new Paginator,
            'counter' => new Counter,
            default => throw new \Exception("Orchestrator is unable to handle: $compare")
        };
    }
}