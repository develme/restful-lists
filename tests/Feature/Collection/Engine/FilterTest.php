<?php

namespace Tests\Feature\Collection\Engine;

use Closure;
use Illuminate\Support\Collection;
use ReflectionException;
use Tests\Contracts\ChecksFilterSupport;
use Tests\Feature\Collection\TestCase;
use Tests\Models\Example;

/**
 * Class FilterTest
 * @package Tests\Feature\Collection\Engine
 * @group Engine
 * @group Filter
 * @group Collection
 */
class FilterTest extends TestCase implements ChecksFilterSupport
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_simplified_filtering(): void
    {
        $filters = ['type' => 'Open'];

        $closed = Example::factory($this->faker->numberBetween(10, 15))->closed()->make()->toArray();
        $open = Example::factory($this->faker->numberBetween(10, 15))->open()->make()->toArray();
        $all = new Collection([...$closed, ...$open]);

        $resources = $all->where('type', 'Open')->values();
        $engine = $this->generateEngine(['data' => $all]);

        $this->assertEquals($resources->count(), $engine->filters($filters)->count());
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_complex_filtering(): void
    {
        $filters = [
            'type' => [
                'field' => 'type',
                'type' => 'not_equals',
                'value' => 'Closed'
            ],
            'email' => [
                'field' => 'email',
                'type' => 'contains',
                'value' => 'example',
            ]
        ];

        $closed = Example::factory($this->faker->numberBetween(10, 15))->closed()->make()->toArray();
        $open = Example::factory($this->faker->numberBetween(10, 15))->open()->make()->toArray();
        $all = new Collection([...$closed, ...$open]);

        $resources = $all->where('type', '!=', 'Closed')->filter(fn($row) => str_contains(strtolower($row['email']), strtolower('example')))->values();
        $engine = $this->generateEngine(['data' => $all]);

        $this->assertEquals($resources->count(), $engine->filters($filters)->count());
    }

    protected function generateCollectionResource(): Closure
    {
        return function ($value) {
            $random = Example::factory($this->faker->numberBetween(10, 15))->make()->toArray();
            $specific = Example::factory($this->faker->numberBetween(16, 25), ['type' => $value])->make()->toArray();

            return new Collection([...$random, ...$specific]);
        };
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_equals(): void
    {
        $search = $this->faker->word;

        $this->checkFilterTypeAgainstResource(
            type: "equals",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', '=', $search)->values(),
            value: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_not_equals(): void
    {
        $search = $this->faker->word;

        $this->checkFilterTypeAgainstResource(
            type: "not_equals",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', '!=', $search)->values(),
            value: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_contains(): void
    {
        $search = $this->faker->word;
        $value = $this->faker->word . " " . $search . " " . $this->faker->word;

        $this->checkFilterTypeAgainstResource(
            type: "contains",
            resourceHandle: fn(Collection $resources): Collection => $resources->filter(fn ($row) => str_contains($row['type'], $search))->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_starts_with(): void
    {
        $search = $this->faker->word;
        $value = $search . " " . $this->faker->word;

        $this->checkFilterTypeAgainstResource(
            type: "starts_with",
            resourceHandle: fn(Collection $resources): Collection => $resources->filter(fn ($row) => str_starts_with($row['type'], $search))->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_ends_with(): void
    {
        $search = $this->faker->word;
        $value = $this->faker->word . " " . $search;

        $this->checkFilterTypeAgainstResource(
            type: "ends_with",
            resourceHandle: fn(Collection $resources): Collection => $resources->filter(fn ($row) => str_ends_with($row['type'], $search))->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_in(): void
    {
        $value = $this->faker->word;
        $search = [$value];

        $this->checkFilterTypeAgainstResource(
            type: "in",
            resourceHandle: fn(Collection $resources): Collection => $resources->whereIn('type', $search)->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_between(): void
    {
        $value = $this->faker->numberBetween(1000, 2000);
        $search['from'] = $this->faker->numberBetween(1000, 1200);
        $search['to'] = $this->faker->numberBetween(1800, 2000);

        $this->checkFilterTypeAgainstResource(
            type: "between",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', ">=", $search['from'])->where('type', "<=", $search['to'])->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_less_than(): void
    {
        $search = $this->faker->numberBetween(11, 15);
        $value = $this->faker->numberBetween(5, 10);

        $this->checkFilterTypeAgainstResource(
            type: "less_than",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', "<", $search)->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_less_than_or_equal(): void
    {
        $search = $this->faker->numberBetween(8, 15);
        $value = $this->faker->numberBetween(5, 12);

        $this->checkFilterTypeAgainstResource(
            type: "less_than_or_equal",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', "<=", $search)->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     */
    public function it_filters_greater_than(): void
    {
        $search = $this->faker->numberBetween(5, 10);
        $value = $this->faker->numberBetween(11, 15);

        $this->checkFilterTypeAgainstResource(
            type: "greater_than",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', ">", $search)->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }

    /**
     * @test
     */
    public function it_filters_greater_than_or_equal(): void
    {
        $search = $this->faker->numberBetween(5, 12);
        $value = $this->faker->numberBetween(8, 15);

        $this->checkFilterTypeAgainstResource(
            type: "greater_than_or_equal",
            resourceHandle: fn(Collection $resources): Collection => $resources->where('type', ">=", $search)->values(),
            value: $value,
            search: $search,
            resourceGenerator: $this->generateCollectionResource()
        );
    }
}