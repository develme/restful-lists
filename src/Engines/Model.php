<?php


namespace DevelMe\RestfulList\Engines;

use Closure;
use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting as SettingInterface;
use DevelMe\RestfulList\Filters\Setting;
use Illuminate\Database\Eloquent\Builder;

class Model implements Data
{
    private Builder $data;

    private array $filters = [];

    private bool $prepared = false;

    private Closure $filterTypeCompare;

    private array $filterTypeComparatorsMap = [
        'equals' => '='
    ];

    private Composer $comparator;

    public function __construct(Builder $data, Composer $comparator)
    {
        $this->data = $data;
        $this->comparator = $comparator;
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function sql()
    {
        return $this->prepareModel()->toSql();
    }

    public function bindings()
    {
        return $this->prepareModel()->getBindings();
    }

    public function count()
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

            $this->prepared = true;
        }

        return $this->data;
    }

    protected function applyFilters()
    {
        foreach ($this->filters as $name => $search) {
            match(gettype($search)) {
                "string" => $this->data->where($name, '=', $search),
                "array" => $this->comparator->compare($this->arrayToSetting($name, $search), $this),
                "object" => $this->comparator->compare($search, $this),
                default => throw new \Exception("Type not supported: " . gettype($search))
            };
        }
    }

    public function data(): Builder
    {
        return $this->data;
    }

    private function arrayToSetting($name, array $search): SettingInterface
    {
        return new Setting(
            field: $search['field'] ?? $name,
            type: $search['type'] ?? 'equals',
            value: $search['value'] ?? $search
        );
    }
}