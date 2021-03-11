<?php


namespace Tests\Unit\Model;


use DevelMe\RestfulList\Engines\Model as ModelEngine;
use DevelMe\RestfulList\Orchestration\Model\Orchestrator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Tests\Traits\WithEngineParts;

abstract class TestCase extends \Tests\TestCase
{
    use WithEngineParts;

    protected array $engine = [
        'engine' => ModelEngine::class,
        'orchestrator' => Orchestrator::class,
    ];

    protected function mockBuilderWithResources(Builder $mock, Collection $resources): void
    {
        $mock->shouldReceive('count')->andReturn($resources->count());
        $mock->shouldReceive('get')->andReturn($resources);
    }
}