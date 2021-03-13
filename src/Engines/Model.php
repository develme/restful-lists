<?php


namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting as FilterSettingInterface;
use DevelMe\RestfulList\Contracts\Orders\Setting as OrderSettingInterface;
use DevelMe\RestfulList\Contracts\Pagination\Setting as PaginationSettingInterface;
use DevelMe\RestfulList\Contracts\Orchestration;
use DevelMe\RestfulList\Filters\Setting as FilterSetting;
use DevelMe\RestfulList\Orders\Setting as OrderSetting;
use DevelMe\RestfulList\Pagination\Setting as PaginationSetting;
use Exception;
use Illuminate\Database\Eloquent\Builder;

final class Model extends Base implements Data
{
    private Builder $data;

    /**
     * This property tracks if filters,
     *
     * @var bool
     */
    private bool $prepared = false;

    public function __construct(Builder $data, Orchestration $orchestrator)
    {
        parent::__construct($orchestrator);

        $this->data = $data;
    }

    public function sql(): string
    {
        $this->prepare();

        return $this->data->toSql();
    }

    public function bindings(): array
    {
        $this->prepare();

        return $this->data->getBindings();
    }

    public function count(): int
    {
        $this->prepare();

        return $this->orchestrator->counter()->count($this);
    }

    protected function prepare()
    {
        if ($this->prepared === false) {
            $this->applyTotal();
            $this->applyFilters();
            $this->applyOrders();
            $this->applyPagination();

            $this->results = $this->data->get();

            $this->prepared = true;
        }
    }

    protected function applyTotal()
    {
        $this->total = $this->orchestrator->counter()->count($this);
    }

    /**
     * @throws Exception
     */
    protected function applyFilters()
    {
        if (!empty($this->filters)) {
            $this->orchestrator->filter()->filter($this, $this->filters);
        }
    }

    /**
     * @throws Exception
     */
    protected function applyOrders()
    {
        if (!empty($this->orders)) {
            $this->orchestrator->order()->arrange($this, $this->orders);
        }
    }

    /**
     * @throws Exception
     */
    protected function applyPagination()
    {
        if (!empty($this->pagination)) {
            $this->orchestrator->pagination()->paginate($this, $this->pagination);
        }
    }

    public function data(): Builder
    {
        return $this->data;
    }
}