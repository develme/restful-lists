<?php


namespace DevelMe\RestfulList\Filters\Model;

use DevelMe\RestfulList\Contracts\Filters\Filtration;
use DevelMe\RestfulList\Contracts\Filters\Setting;
use Illuminate\Database\Eloquent\Builder;

class Contains implements Filtration
{
    public function filter($data, Setting $setting): Builder
    {
        $data->where(
            $setting->field(),
            'like',
            $this->prepValue((string)$setting->value())
        );

        return $data;
    }

    private function prepValue(string $value): string
    {
        return "%$value%";
    }
}