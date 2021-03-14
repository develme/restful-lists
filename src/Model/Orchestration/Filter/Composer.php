<?php

namespace DevelMe\RestfulList\Model\Orchestration\Filter;

use DevelMe\RestfulList\Concerns\Composition\WithFiltration;
use DevelMe\RestfulList\Contracts\Comparator\Composer as ComposerInterface;
use DevelMe\RestfulList\Contracts\Registration;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class Composer implements ComposerInterface, Registration
{
    use WithFiltration;

    protected static string $defaults = Composition::class;

    public function compare(Setting $setting, Data $data)
    {
        $this->resolveFiltration($setting)->filter($data->data(), $setting);
    }
}