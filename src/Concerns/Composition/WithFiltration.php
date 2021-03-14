<?php


namespace DevelMe\RestfulList\Concerns\Composition;


use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;

trait WithFiltration
{
    use WithDefaults;

    public function resolveFiltration(Setting $setting): Filtration
    {
        $compositions = $this->compositions;

        return $compositions($setting->type());
    }
}