<?php


namespace DevelMe\RestfulList\Engines;

use Closure;
use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Orders\Arrangement;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting as FilterSettingInterface;
use DevelMe\RestfulList\Contracts\Orders\Setting as OrderSettingInterface;
use DevelMe\RestfulList\Filters\Setting as FilterSetting;
use DevelMe\RestfulList\Orders\Setting as OrderSetting;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class Model implements Data
{
    private Builder $data;

    private array $filters = [];

    private array $orders = [];

    private bool $prepared = false;

    private Closure $filterTypeCompare;

    private array $filterTypeComparatorsMap = [
        'equals' => '='
    ];

    private Composer $comparator;

    private Arrangement $arrangement;

    public function __construct(Builder $data, Composer $comparator, Arrangement $arrangement)
    {
        $this->data = $data;
        $this->comparator = $comparator;
        $this->arrangement = $arrangement;
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

    public function sql(): string
    {
        return $this->prepareModel()->toSql();
    }

    public function bindings(): array
    {
        return $this->prepareModel()->getBindings();
    }

    public function count(): int
    {
        return $this->prepareModel()->count();
    }

    public function go()
    {
        return $this->prepareModel()->get();
    }

    public function prepareModel()
    {
        if ($this->prepared === false) {
            $this->applyFilters();
            $this->applyOrders();

            $this->prepared = true;
        }

        return $this->data;
    }

    /**
     * @throws Exception
     */
    protected function applyFilters()
    {
        foreach ($this->filters as $name => $setting) {
            match(true) {
                is_string($setting) => $this->data->where($name, '=', $setting),
                is_array($setting) => $this->comparator->compare($this->convertArrayToFilterSetting($name, $setting), $this),
                $setting instanceof FilterSettingInterface => $this->comparator->compare($setting, $this),
                default => throw new Exception("Type not supported: " . gettype($setting))
            };
        }
    }

    /**
     * @throws Exception
     */
    protected function applyOrders()
    {
        foreach ($this->orders as $name => $setting) {
            match(true) {
                is_string($setting) && is_numeric($name) => $this->data->orderBy($setting),
                is_string($setting) && is_string($name) => $this->data->orderBy($name, $setting),
                is_array($setting) => $this->arrangement->arrange($this->convertArrayToOrderSetting($name, $setting), $this),
                $setting instanceof OrderSettingInterface => $this->arrangement->compare($setting, $this),
                default => throw new Exception("Type not supported: " . gettype($setting))
            };
        }
    }

    public function data(): Builder
    {
        return $this->data;
    }

    private function convertArrayToFilterSetting($name, array $setting): FilterSettingInterface
    {
        return new FilterSetting(
            field: $setting['field'] ?? $name,
            type: $setting['type'] ?? 'equals',
            value: $setting['value'] ?? $setting
        );
    }

    private function convertArrayToOrderSetting($name, array $setting): OrderSettingInterface
    {
        return new OrderSetting(
            field: $setting['field'] ?? $name,
            direction: $setting['direction'] ?? 'asc',
        );
    }
}