<?php

namespace Tests;

use Illuminate\Container\Container;
use Tests\Traits\WithFaker;
use Tests\Traits\WithEloquent;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $traits = array_flip(class_uses_recursive(static::class));

        $this->container = new Container;

        Container::setInstance($this->container);

        if (isset($traits[WithFaker::class])) {
            $this->setupFaker();
        }

        if (isset($traits[WithEloquent::class])) {
            $this->setupEloquent();
        }
    }

    protected function tearDown(): void
    {
        $traits = array_flip(class_uses_recursive(static::class));

        if (isset($traits[WithFaker::class])) {
            $this->teardownFaker();
        }

        if (isset($traits[WithEloquent::class])) {
            $this->teardownEloquent();
        }

        \Mockery::close();
    }
}