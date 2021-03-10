<?php


namespace Tests\Unit\Model\Engine;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use ReflectionException;
use Tests\Models\Example;
use Tests\Traits\WithEloquent;
use Tests\Traits\WithFaker;
use Tests\Unit\Model\TestCase;

/**
 * Class ModelEngineTest
 * @package Tests\Unit
 * @group Model
 * @group Engine
 * @group Filter
 */
class FilterTest extends TestCase
{
    use WithFaker, WithEloquent;

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_returns_all()
    {
        $resource = Example::factory($this->faker->numberBetween(5, 15))->make();
        $mocks = $this->generateEngineMock(function ($mocks) use ($resource) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('get')->once()->andReturn($resource);
                    $mock->shouldReceive('count')->once()->andReturn($resource->count());
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngine(
            filters: [],
            mocks: $mocks,
            method: function ($engine) use ($resource) {
                $results = $engine->go();

                $this->assertEquals($resource->count(), $results->count());
                $this->assertEquals($resource->count(), $engine->count());
                $this->assertEquals($resource->first()->name, $results->first()->name);
            }
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_simplified_filtering(): void
    {
        $filters = ['type' => "Test Type", "status" => "Test"];
        $count = $this->faker->numberBetween(5, 20);

        $mocks = $this->generateEngineMock(function ($mocks) use ($count) {
            foreach ($mocks as $mock) {
                /** @var MockInterface $mock */
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('where')->with('type', '=', 'Test Type')->once();
                    $mock->shouldReceive('where')->with('status', '=', 'Test')->once();
                    $mock->shouldReceive('count')->once()->andReturn($count);
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngine(
            filters: $filters,
            mocks: $mocks,
            method: fn($engine, \Tests\TestCase $tester) => $tester->assertEquals($count, $engine->count())
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_complex_filtering()
    {
        $filters = [
            'type' => [
                'field' => 'type',
                'type' => 'contains',
                'value' => 'Test Type'
            ],
            'status' => [
                'field' => 'status',
                'type' => 'not_equals',
                'value' => 'Test'
            ],
        ];
        $count = $this->faker->numberBetween(5, 15);
        $mocks = $this->generateEngineMock(function ($mocks) use ($count) {
            foreach ($mocks as $mock) {
                /** @var MockInterface $mock */
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('where')->with('type', 'like', '%Test Type%')->once();
                    $mock->shouldReceive('where')->with('status', '!=', 'Test')->once();
                    $mock->shouldReceive('count')->once()->andReturn($count);
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngine(
            filters: $filters,
            mocks: $mocks,
            method: fn($engine, TestCase $tester) => $tester->assertEquals($count, $engine->count())
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_equals()
    {
        $search = $this->faker->word;

        $this->checkFilterType(
            type: "equals",
            value: $search,
            mockHandle: fn($mock) => $mock->shouldReceive('where')->with('type', '=', "$search")->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_not_equals()
    {
        $search = $this->faker->word;

        $this->checkFilterType(
            type: "not_equals",
            value: $search,
            mockHandle: fn($mock) => $mock->shouldReceive('where')->with('type', '!=', "$search")->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_contains()
    {
        $search = $this->faker->word;

        $this->checkFilterType(
            type: "contains",
            value: $search,
            mockHandle: fn($mock) => $mock->shouldReceive('where')->with('type', 'like', "%$search%")->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_starts_with()
    {
        $search = $this->faker->word;

        $this->checkFilterType(
            type:'starts_with',
            value: $search,
            mockHandle: fn($mock) => $mock->shouldReceive('where')->with('type', 'like', "%$search")->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_ends_with()
    {
        $search = $this->faker->word;

        $this->checkFilterType(
            type: 'ends_with',
            value: $search,
            mockHandle: fn($mock) => $mock->shouldReceive('where')->with('type', 'like', "$search%")->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_in()
    {
        $search = $this->faker->words;

        $this->checkFilterType(
            type: 'in',
            value: $search,
            mockHandle: fn($mock) => $mock->shouldReceive('whereIn')->with('type', $search)->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_between()
    {
        $start = Carbon::parse($this->faker->dateTime);
        $search['from'] = $start->format('Y-m-d');
        $search['to'] = $start->addDays($this->faker->numberBetween(5, 15))->format('Y-m-d');

        $this->checkFilterType(
            type: 'between',
            value: $search,
            mockHandle: function ($mock) use ($search) {
                $mock->shouldReceive('where')->with('type', '>=', $search['from'])->once();
                $mock->shouldReceive('where')->with('type', '<=', $search['to'])->once();
            }
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_less_than()
    {
        $search = $this->faker->numberBetween(5, 10);

        $this->checkFilterType(
            type: 'less_than',
            value: $search,
            mockHandle: fn ($mock) => $mock->shouldReceive('where')->with('type', '<', $search)->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_less_than_or_equal()
    {
        $search = $this->faker->numberBetween(5, 10);

        $this->checkFilterType(
            type: 'less_than_or_equal',
            value: $search,
            mockHandle: fn ($mock) => $mock->shouldReceive('where')->with('type', '<=', $search)->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_greater_than()
    {
        $search = $this->faker->numberBetween(5, 10);

        $this->checkFilterType(
            type: 'greater_than',
            value: $search,
            mockHandle: fn ($mock) => $mock->shouldReceive('where')->with('type', '>', $search)->once()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_filters_greater_than_or_equal()
    {
        $search = $this->faker->numberBetween(5, 10);

        $this->checkFilterType(
            type: 'greater_than_or_equal',
            value: $search,
            mockHandle: fn ($mock) => $mock->shouldReceive('where')->with('type', '>=', $search)->once()
        );
    }
}