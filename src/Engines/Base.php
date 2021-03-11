<?php


namespace DevelMe\RestfulList\Engines;


use DevelMe\RestfulList\Contracts\Orchestration;

abstract class Base
{
    protected Orchestration $orchestrator;

    protected array $filters = [];

    protected array $orders = [];

    protected array $pagination = [];

    protected int $total;

    protected mixed $results;

    public function __construct(Orchestration $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function orders(array $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function pagination(array $pagination): self
    {
        $this->pagination = $pagination;

        return $this;
    }

    public function total(): int
    {
        $this->prepare();

        return $this->total;
    }

    public function go()
    {
        $this->prepare();

        return $this->results;
    }


    abstract protected function prepare();
}