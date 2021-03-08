<?php


namespace Tests\Unit;


use Carbon\Carbon;
use Closure;
use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Comparator\Registration;
use DevelMe\RestfulList\Engines\Model as ModelEngine;
use DevelMe\RestfulList\Filters\ModelComposer;
use Exception;
use Illuminate\Container\Util;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;
use ReflectionException;
use Tests\Models\Example;
use Tests\TestCase;
use Tests\Traits\WithEloquent;
use Tests\Traits\WithFaker;

class EngineTest extends TestCase
{
    use WithFaker, WithEloquent;

    protected array $engines = [
        'model' =>  [
            'engine' => ModelEngine::class,
            'composer' => ModelComposer::class
        ]
//        'array' => ArrayEngine::class,
    ];

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_returns_all()
    {
        $resources['model'] = Example::factory($this->faker->numberBetween(5, 15))->make();
        $mocks['model'] = $this->generateEngineMock('model', function ($mocks) use ($resources) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('get')->once()->andReturn($resources['model']);
                    $mock->shouldReceive('count')->once()->andReturn($resources['model']->count());
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngines(
            filters: [],
            data: $mocks,
            method: function ($engine) use ($resources) {
                match($engine::class) {
                    ModelEngine::class => $this->checkEngine($engine, $resources['model'])
                };

            });
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_simplified_filtering(): void
    {
        $filters = ['type' => "Test Type", "status" => "Test"];
        $count = $this->faker->numberBetween(5, 20);

        $mocks['model'] = $this->generateEngineMock('model', function ($mocks) use ($count) {
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

        $this->checkFiltersAgainstEngines(
            filters: $filters,
            data: $mocks,
            result: $count
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
        $mocks['model'] = $this->generateEngineMock('model', function ($mocks) use ($count) {
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

        $this->checkFiltersAgainstEngines(
            filters: $filters,
            data: $mocks,
            result: $count
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

    /**
     * @param string $type
     * @param mixed $value
     * @param Closure $mockHandle
     *
     * @throws ReflectionException
     */
    protected function checkFilterType(string $type, mixed $value, Closure $mockHandle)
    {
        $filter = [
            'type' => [
                'field' => 'type',
                'type' => $type,
                'value' => $value,
            ]
        ];
        $count = $this->faker->numberBetween(5, 15);
        $mocks['model'] = $this->generateEngineMock('model', function ($mocks) use ($count, $mockHandle) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mockHandle($mock);
                    $mock->shouldReceive('count')->once()->andReturn($count);
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngines(
            filters: $filter,
            data: $mocks,
            result: $count
        );
    }

    /**
     * @param array $filters
     * @param array $data
     * @param Closure|string $method
     * @param int $result
     *
     * @throws ReflectionException
     * @throws Exception
     */
    protected function checkFiltersAgainstEngines(array $filters, array $data, Closure|string $method = 'count', $result = 0)
    {
        foreach ($this->engines as $type => $setting) {
            $datum = $data[$type] ?? null;
            if (!is_null($datum)) {
                $engine = (new ReflectionClass($setting['engine']))->newInstanceArgs($datum);

                $engine->filters($filters);

                if ($method instanceof Closure) {
                    $method($engine);
                } else {
                    $this->assertEquals($result, call_user_func([$engine, $method]));
                }
            } else {
                throw new Exception("Engine type $type not supported by the test");
            }
        }
    }

    protected function checkEngine($engine, $resources): bool {
        $results = $engine->go();

        $this->assertEquals($resources->count(), $results->count());
        $this->assertEquals($resources->count(), $engine->count());
        $this->assertEquals($resources->first()->name, $results->first()->name);

        return true;
    }

    /**
     * @param string $type
     * @param Closure $handle
     *
     * @return MockInterface[]
     *
     * @throws ReflectionException
     * @throws Exception
     */
    private function generateEngineMock(string $type, Closure $handle): array
    {
        if (!isset($this->engines[$type])) {
            throw new Exception("Engine type $type not supported");
        }

        $mocks = [];
        $setting = $this->engines[$type];
        $reflection = new ReflectionClass($setting['engine']);

        $parameters = $reflection->getConstructor()?->getParameters();

        foreach ($parameters as $dependency) {
            $name = Util::getParameterClassName($dependency);

            $dependency = new ReflectionClass($name);

            if ($dependency->implementsInterface(Composer::class)) {
                $composer = new ReflectionClass($setting['composer']);

                $instance = $composer->newInstance();

                if ($composer->implementsInterface(Registration::class)) {
                    $instance->register();
                }

                $mocks[] = $instance;
            } else {
                $mocks[] = Mockery::mock($name);
            }
        }

        return $handle($mocks);
    }
}