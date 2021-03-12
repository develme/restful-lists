<?php

namespace Tests\Traits;


use Faker\Factory;
use Faker\Generator;

trait WithFaker
{
    protected Generator $faker;

    protected function setupFaker(): void
    {
        $this->faker = $this->instantiateFaker();

        if (!$this->container->bound(Generator::class)) {
            $this->container->singleton(Generator::class, function ($container, $parameters) {
                $locale = $parameters['locale'] ?? env('faker_local', Factory::DEFAULT_LOCALE);

                return Factory::create($locale);
            });
        }
    }

    protected function teardownFaker(): void
    {

    }

    protected function faker($locale = null): Generator
    {
        return is_null($locale) ? $this->faker : $this->instantiateFaker($locale);
    }

    protected function instantiateFaker($locale = null): Generator
    {
        return Factory::create($locale ?? env('faker_local', Factory::DEFAULT_LOCALE));
    }
}