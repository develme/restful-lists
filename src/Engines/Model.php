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

        return $this->results->count();
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
        $filtration = $this->orchestrator->filter();

        if (!empty($this->filters)) {
            $filtration->filter($this, $this->filters);
        }
    }

    /**
     * @throws Exception
     */
    protected function applyOrders()
    {
        $arrangement = $this->orchestrator->order();

        if (!empty($this->orders)) {
            $arrangement->arrange($this, $this->orders);
        }
    }

    /**
     * @throws Exception
     */
    protected function applyPagination()
    {
        $paginator = $this->orchestrator->pagination();
        $setting = $this->pagination;

        if (!empty($setting)) {
            $setting = match (true) {
                !array_is_list($setting) => PaginationSetting::createFromAssociative($setting),
                array_is_list($setting) => PaginationSetting::createFromOrdered($setting),
                $setting instanceof PaginationSettingInterface => $setting,
                default => throw new Exception("Type not supported: " . gettype($setting))
            };

            $paginator->paginate($setting, $this);
        }
    }

    public function data(): Builder
    {
        return $this->data;
    }
}