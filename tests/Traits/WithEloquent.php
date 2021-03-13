<?php

namespace Tests\Traits;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Container\Container;

trait WithEloquent
{

    protected function setupEloquent()
    {
        $eloquent = new Manager;

        $directory = getcwd();

        $eloquent->addConnection([
            'driver'    => 'sqlite',
            'host'      => 'localhost',
            'database'  => "$directory/tests/database/example.sqlite",
            'prefix'    => '',
        ]);

        $eloquent->setAsGlobal();
        $eloquent->bootEloquent();

        if (method_exists($this, 'migrateDatabase')) {
            $this->migrateDatabase($eloquent);
        }
    }

    protected function teardownEloquent(): void
    {
        $directory = getcwd();

        file_put_contents("$directory/tests/database/example.sqlite", "");
    }
}