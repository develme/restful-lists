<?php


namespace Tests\Unit\Model;


use DevelMe\RestfulList\Engines\Model as ModelEngine;
use DevelMe\RestfulList\Model\Orchestration\Orchestrator;
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

    protected function mockBuilderWithResources(Builder $mock, Collection $resources, ?array $counts = null): void
    {
        $counts = $counts ?? [$resources->count()];
        $mock->shouldReceive('count')->andReturn(...$counts);
        $mock->shouldReceive('get')->andReturn($resources);
    }
}