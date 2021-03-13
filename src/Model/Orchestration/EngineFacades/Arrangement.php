<?php


namespace DevelMe\RestfulList\Model\Orchestration\EngineFacades;


use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Orders\Setting as OrderSettingInterface;
use DevelMe\RestfulList\Model\Orchestration\Order\Arrangement as SingleArrangement;
use DevelMe\RestfulList\Orders\Setting as OrderSetting;
use Exception;

class Arrangement implements \DevelMe\RestfulList\Contracts\Engine\Arrangement
{
    private SingleArrangement $arrangement;

    public function __construct(SingleArrangement $arrangement)
    {
        $this->arrangement = $arrangement;
    }

    public function arrange(Data $data, array $orders)
    {
        foreach ($orders as $name => $setting) {
            $setting = match(true) {
                is_string($setting) && is_numeric($name) => OrderSetting::createFromString($setting),
                is_string($setting) && is_string($name) => OrderSetting::createFromString($name, $setting),
                is_array($setting) => OrderSetting::createFromArray($setting, $name),
                $setting instanceof OrderSettingInterface => $setting,
                default => throw new Exception("Type not supported: " . gettype($setting))
            };

            $this->arrangement->arrange($setting, $data);
        }
    }
}