<?php


namespace Tests\Feature\Collection;


use DevelMe\RestfulList\Engines\Collection;
use DevelMe\RestfulList\Collection\Orchestration\Orchestrator;
use Tests\Traits\WithEngineParts;
use Tests\Traits\WithFaker;

abstract class TestCase extends \Tests\TestCase
{
    use WithEngineParts, WithFaker;

    protected array $engine = [
        'engine' => Collection::class,
        'orchestrator' => Orchestrator::class,
    ];
}