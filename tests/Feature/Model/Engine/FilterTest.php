<?php


namespace Tests\Feature\Model\Engine;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use ReflectionException;
use Tests\Contracts\ChecksFilterSupport;
use Tests\Feature\Model\TestCase;
use Tests\Models\Example;
use Closure;

/**
 * Class FilterTest
 * @package Tests\Feature\Model\Engine
 * @group Filter
 * @group Model
 * @group Engine
 */
class FilterTest extends TestCase implements ChecksFilterSupport
{
    /**
     * @test
     * @throws Exception
     */
    public function it_has_simplified_filtering(): void
    {
        $filters = ['type' => 'Open'];

        Example::factory($this->faker->numberBetween(10, 15))->closed()->create();
        Example::factory($this->faker->numberBetween(10, 15))->open()->create();

        $resources = Example::where('type', 'Open')->get();
        $engine = $this->generateEngine(['data' => Example::query()]);

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

        Example::factory($this->faker->numberBetween(10, 15))->closed()->create();
        Example::factory($this->faker->numberBetween(10, 15))->open()->create();

        $resources = Example::where('type', '!=', 'Closed')->where('email', 'like', '%example%')->get();
        $engine = $this->generateEngine(['data' => Example::query()]);

        $this->assertEquals($resources->count(), $engine->filters($filters)->count());
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '=', $search)->get(),
            value: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '!=', $search)->get(),
            value: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', 'like', "%$search%")->get(),
            value: $value,
            search: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', 'like', "$search%")->get(),
            value: $value,
            search: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', 'like', "%$search")->get(),
            value: $value,
            search: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->whereIn('type', $search)->get(),
            value: $value,
            search: $search
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_between(): void
    {
        $date = $this->faker->dateTime;
        $start = Carbon::parse($date);
        $search['from'] = $start->format('Y-m-d');
        $value = $start->addDays($this->faker->numberBetween(5, 10))->format('Y-m-d');
        $search['to'] = Carbon::parse($date)->addDays($this->faker->numberBetween(15, 20))->format('Y-m-d');

        $this->checkFilterTypeAgainstResource(
            type: "between",
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '>=', $search['from'])->where('type', '<=', $search['to'])->get(),
            value: $value,
            search: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '<', $search)->get(),
            value: $value,
            search: $search
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
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '<=', $search)->get(),
            value: $value,
            search: $search
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_greater_than(): void
    {
        $search = $this->faker->numberBetween(5, 10);
        $value = $this->faker->numberBetween(11, 15);

        $this->checkFilterTypeAgainstResource(
            type: "greater_than",
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '>', $search)->get(),
            value: $value,
            search: $search
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_greater_than_or_equal(): void
    {
        $search = $this->faker->numberBetween(5, 12);
        $value = $this->faker->numberBetween(8, 15);

        $this->checkFilterTypeAgainstResource(
            type: "greater_than_or_equal",
            resourceHandle: fn(Builder $query): Collection => $query->where('type', '>=', $search)->get(),
            value: $value,
            search: $search
        );
    }
}