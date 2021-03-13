<?php


namespace DevelMe\RestfulList\Model\Orchestration;


use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Model\Orchestration\Count\Counter;
use DevelMe\RestfulList\Model\Orchestration\EngineFacades\Filtration;
use DevelMe\RestfulList\Model\Orchestration\Filter\Composer;
use DevelMe\RestfulList\Model\Orchestration\EngineFacades\Arrangement as ArrangementFacade;
use DevelMe\RestfulList\Model\Orchestration\Order\Arrangement;
use DevelMe\RestfulList\Model\Orchestration\Pagination\Paginator;
use DevelMe\RestfulList\Model\Orchestration\EngineFacades\Paginator as PaginationFacade;

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
            'order' => new ArrangementFacade(new Arrangement),
            'pagination' => new PaginationFacade(new Paginator),
            'counter' => new Counter,
            default => throw new \Exception("Orchestrator is unable to handle: $compare")
        };
    }
}