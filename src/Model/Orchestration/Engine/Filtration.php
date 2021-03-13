<?php


namespace DevelMe\RestfulList\Model\Orchestration\Engine;

use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting as FilterSettingInterface;
use DevelMe\RestfulList\Filters\Setting as FilterSetting;
use Exception;

class Filtration implements \DevelMe\RestfulList\Contracts\Engine\Filtration
{
    protected Composer $comparator;

    public function __construct(Composer $comparator)
    {
        $this->comparator = $comparator;
    }

    public function filter(Data $data, array $filters)
    {
        foreach ($filters as $name => $setting) {
            $setting = match(true) {
                is_string($setting) => FilterSetting::createFromString($setting, $name),
                is_array($setting) => FilterSetting::createFromArray($setting, $name),
                $setting instanceof FilterSettingInterface => $setting,
                default => throw new Exception("Type not supported: " . gettype($setting))
            };

            $this->comparator->compare($setting, $data);
        }
    }
}