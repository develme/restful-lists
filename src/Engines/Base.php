<?php


namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orchestration;

abstract class Base implements Data
{
    protected Orchestration $orchestrator;

    protected array $filters = [];

    protected array $orders = [];

    protected array $pagination = [];

    protected int $total;

    protected mixed $results;

    /**
     * This property tracks if totals, filters, orders, pagination, and results have been fetched
     */
    protected bool $prepared = false;

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

    public function count(): int
    {
        $this->prepare();

        return $this->orchestrator->counter()->count($this);
    }

    public function go()
    {
        $this->prepare();

        return $this->results;
    }

    public function data()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    protected function prepare()
    {
        if ($this->prepared === false) {
            $this->applyTotal();
            $this->applyFilters();
            $this->applyOrders();
            $this->applyPagination();

            $this->results = $this->orchestrator->result()->get($this);

            $this->prepared = true;
        }
    }

    public function prepared(): bool
    {
        return $this->prepared;
    }

    protected function applyTotal()
    {
        $this->total = $this->orchestrator->counter()->count($this);
    }

    protected function applyFilters()
    {
        if (!empty($this->filters)) {
            $this->orchestrator->filter()->filter($this, $this->filters);
        }
    }

    protected function applyOrders()
    {
        if (!empty($this->orders)) {
            $this->orchestrator->order()->arrange($this, $this->orders);
        }
    }

    protected function applyPagination()
    {
        if (!empty($this->pagination)) {
            $this->orchestrator->pagination()->paginate($this, $this->pagination);
        }
    }
}