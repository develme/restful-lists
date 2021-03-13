<?php


namespace DevelMe\RestfulList\Model\Orchestration;


use Closure;
use DevelMe\RestfulList\Contracts\Defaults;
use DevelMe\RestfulList\Model\Orchestration\Filter\Composer;
use DevelMe\RestfulList\Model\Orchestration\Order\Arrangement;

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
            'filter' => $composer,
            'order' => new Arrangement,
            default => throw new \Exception("Orchestrator is unable to handle: $compare")
        };
    }
}