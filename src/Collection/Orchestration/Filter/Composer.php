<?php


namespace DevelMe\RestfulList\Collection\Orchestration\Filter;

use DevelMe\RestfulList\Concerns\Composition\WithFiltration;
use DevelMe\RestfulList\Contracts\Comparator\Composer as ComposerInterface;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting;

class Composer implements ComposerInterface
{
    use WithFiltration;

    protected static string $defaults = Composition::class;

    public function compare(Setting $setting, Data $data)
    {
        $result = $this->resolveFiltration($setting)->filter($data->data(), $setting);

        $data->setData($result);
    }
}