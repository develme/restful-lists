<?php

namespace DevelMe\RestfulList\Model\Orchestration\Filter;

use Closure;
use DevelMe\RestfulList\Contracts\Comparator\Composer as ComposerContract;
use DevelMe\RestfulList\Contracts\Registration;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;
use ReflectionClass;

class Composer implements ComposerContract, Registration
{
    protected Closure $compositions;

    protected static string $defaults = Composition::class;

    public function register(?Closure $compositions = null): void
    {
        $this->compositions = $compositions ?? $this->defaults();
    }

    public function compare(Setting $setting, Data $data): mixed
    {
        return $this->resolveFiltration($setting)->filter($data->data(), $setting);
    }

    private function defaults(): Closure
    {
        return ((new ReflectionClass(static::$defaults))->newInstance())->defaults();
    }

    public function resolveFiltration(Setting $setting): Filtration
    {
        $compositions = $this->compositions;

        return $compositions($setting->type());
    }
}