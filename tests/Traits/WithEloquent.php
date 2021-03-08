<?php

namespace Tests\Traits;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;

trait WithEloquent
{

    public function setupEloquent()
    {

        $capsule = new Capsule;

        $directory = getcwd();

        $capsule->addConnection([
            'driver'    => 'sqlite',
            'host'      => 'localhost',
            'database'  => "$directory/tests/database/example.sqlite",
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}