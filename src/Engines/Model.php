<?php


namespace DevelMe\RestfulList\Engines;

use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting as FilterSettingInterface;
use DevelMe\RestfulList\Contracts\Orchestration;
use DevelMe\RestfulList\Contracts\Orders\Setting as OrderSettingInterface;
use DevelMe\RestfulList\Filters\Setting as FilterSetting;
use DevelMe\RestfulList\Orders\Setting as OrderSetting;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class Model extends Base implements Data
{
    private Builder $data;

    private array $filters = [];

    private array $orders = [];

    private array $pagination = [];

    private int $total;

    /**
     * This property tracks if filters,
     *
     * @var bool
     */
    private bool $prepared = false;

    private Collection $results;

    public function __construct(Builder $data, Orchestration $orchestrator)
    {
        parent::__construct($orchestrator);

        $this->data = $data;
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
            match(true) {
                is_string($setting) && is_numeric($name) => $this->data->orderBy($setting),
                is_string($setting) && is_string($name) => $this->data->orderBy($name, $setting),
                is_array($setting) => $arrangement->arrange($this->convertArrayToOrderSetting($name, $setting), $this),
                $setting instanceof OrderSettingInterface => $arrangement->compare($setting, $this),
                default => throw new Exception("Type not supported: " . gettype($setting))
            };
        }
    }

    protected function applyPagination()
    {
        if (!empty($this->pagination)) {
            list($start, $end) = match (true) {
                isset($this->pagination['start']) && isset($this->pagination['end']) => [$this->pagination['start'], $this->pagination['end']],
                default => [$this->pagination[0], $this->pagination[1]]
            };

            $this->data->skip($start)->limit($end);
        }
    }

    public function data(): Builder
    {
        return $this->data;
    }

    private function convertArrayToOrderSetting($name, array $setting): OrderSettingInterface
    {
        return new OrderSetting(
            field: $setting['field'] ?? $name,
            direction: $setting['direction'] ?? 'asc',
        );
    }
}