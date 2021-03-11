<?php

namespace DevelMe\RestfulList\Orchestration\Model;

use Closure;
use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Comparator\Registration;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;
use DevelMe\RestfulList\Filters\Model\Map;
use ReflectionClass;

class Composer implements Composer, Registration
{
    protected Closure $compositions;

    protected static string $defaults = Map::class;

    public function register(?Closure $compositions = null): void
    {
        $this->compositions = $compositions ?? $this->defaults();
    }

    public function compare(\DevelMe\RestfulList\Contracts\Filters\Setting $setting, Data $data): mixed
    {
        return $this->resolveFiltration($setting)->filter($data->data(), $setting);
    }

    protected function defaults(): Closure
    {
        return ((new ReflectionClass(static::$defaults))->newInstance())->defaults();
    }

    public function resolveFiltration(Setting $setting): Filtration
    {
        $compositions = $this->compositions;

        return $compositions($setting->type());
    }
}