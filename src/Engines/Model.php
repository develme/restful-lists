<?php


namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Comparator\Composer;
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
        $this->total = $this->data->count();
    }

    /**
     * @throws Exception
     */
    protected function applyFilters()
    {
        /** @var Composer $handler */
        $composer = $this->orchestrator->orchestrate('filter');

        foreach ($this->filters as $name => $setting) {
            $setting = match(true) {
                is_string($setting) => FilterSetting::createFromString($setting, $name),
                is_array($setting) => FilterSetting::createFromArray($setting, $name),
                $setting instanceof FilterSettingInterface => $setting,
                default => throw new Exception("Type not supported: " . gettype($setting))
            };

            $composer->compare($setting, $this);
        }
    }

    /**
     * @throws Exception
     */
    protected function applyOrders()
    {
        $arrangement = $this->orchestrator->orchestrate('order');

        foreach ($this->orders as $name => $setting) {
            $setting = match(true) {
                is_string($setting) && is_numeric($name) => OrderSetting::createFromString($setting),
                is_string($setting) && is_string($name) => OrderSetting::createFromString($name, $setting),
                is_array($setting) => OrderSetting::createFromArray($setting, $name),
                $setting instanceof OrderSettingInterface => $setting,
                default => throw new Exception("Type not supported: " . gettype($setting))
            };

            $arrangement->arrange($setting, $this);
        }
    }

    /**
     * @throws Exception
     */
    protected function applyPagination()
    {
        $paginator = $this->orchestrator->orchestrate('pagination');
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