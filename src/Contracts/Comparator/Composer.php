<?php


namespace DevelMe\RestfulList\Contracts\Comparator;

use DevelMe\RestfulList\Contracts\Engine\Data;
use DevelMe\RestfulList\Contracts\Filters\Setting;

interface Composer
{
    public function compare(Setting $setting, Data $data);
}