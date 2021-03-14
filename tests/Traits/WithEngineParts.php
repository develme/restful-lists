<?php


namespace Tests\Traits;


use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Orchestration;
use DevelMe\RestfulList\Contracts\Registration;
use DevelMe\RestfulList\Contracts\Orders\Arrangement;
use DevelMe\RestfulList\Engines\Base;
use Illuminate\Container\Util;
use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Closure;
use Exception;
use Mockery;
use ReflectionClass;
use ReflectionException;
use Tests\Models\Example;
use Tests\TestCase;

trait WithEngineParts
{
    /**
     * @param Closure $handle
     *
     * @return MockInterface[]
     *
     * @throws ReflectionException
     * @throws Exception
     */
    protected function generateEngineMock(Closure $handle): array
    {
        if (!isset($this->engine)) {
            throw new Exception("Engine property not configured");
        }

        $setting = $this->engine;
        $reflection = new ReflectionClass($setting['engine']);

        $mocks = $this->resolveEngineDependencies($reflection->getConstructor()?->getParameters(), $setting);

        return $handle($mocks);
    }

    /**
     * @param array $arguments
     * @return Base
     * @throws ReflectionException
     * @throws Exception
     */
    protected function generateEngine(array $arguments = []): Base
    {
        if (!isset($this->engine)) {
            throw new Exception("Engine property not configured");
        }

        $setting = $this->engine;
        $reflection = new ReflectionClass($setting['engine']);

        $dependencies = $this->resolveEngineDependencies($reflection->getConstructor()?->getParameters(), $setting, $arguments);

        return $this->instantiateEngine($dependencies);
    }

    /**
     * @param $setting
     * @return Composer
     * @throws ReflectionException
     */
    protected function generateOrchestratorInstance($setting): Orchestration
    {
        $composer = new ReflectionClass($setting['orchestrator']);

        /** @var Orchestration $instance */
        $instance = $composer->newInstance();

        if ($composer->implementsInterface(Registration::class)) {
            $instance->register();
        }

        return $instance;
    }

    /**
     * @param array $filters
     * @param array $mocks
     * @param Closure|null $method
     *
     * @throws ReflectionException
     */
    protected function checkFiltersAgainstEngine(array $filters, array $mocks, ?Closure $method = null)
    {
        $engine = $this->instantiateEngine($mocks);

        $engine->filters($filters);

        if ($method instanceof Closure) {
            $method($engine, $this);
        }
    }

    /**
     * @param array $orders
     * @param array $mocks
     * @param Closure|null $method
     *
     * @throws ReflectionException
     */
    protected function checkOrdersAgainstEngine(array $orders, array $mocks, ?Closure $method = null)
    {
        $engine = $this->instantiateEngine($mocks);

        $engine->orders($orders);

        if ($method instanceof Closure) {
            $method($engine, $this);
        }
    }

    /**
     * @param array $pagination
     * @param array $mocks
     * @param Closure|null $method
     *
     * @throws ReflectionException
     */
    protected function checkPaginationAgainstEngine(array $pagination, array $mocks, ?Closure $method = null)
    {
        $engine = $this->instantiateEngine($mocks);

        $engine->pagination($pagination);

        if ($method instanceof Closure) {
            $method($engine, $this);
        }
    }

    /**
     * @param string $type
     * @param mixed $value
     * @param Closure $mockHandle
     *
     * @throws ReflectionException
     */
    protected function checkFilterTypeAgainstMock(string $type, mixed $value, Closure $mockHandle)
    {
        $filter = [
            'type' => [
                'field' => 'type',
                'type' => $type,
                'value' => $value,
            ]
        ];
        $resources = Example::factory($this->faker->numberBetween(5, 20))->make();

        $mocks = $this->generateEngineMock(function ($mocks) use ($resources, $mockHandle) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mockHandle($mock);
                    $this->mockBuilderWithResources($mock, $resources);
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngine(
            filters: $filter,
            mocks: $mocks,
            method: fn($engine, TestCase $tester) => $tester->assertEquals($resources->count(), $engine->count())
        );
    }

    /**
     * @param string $type
     * @param Closure $resourceHandle
     * @param mixed $value
     * @param mixed $search
     * @param Closure|null $resourceGenerator
     * @throws ReflectionException
     */
    protected function checkFilterTypeAgainstResource(
        string $type,
        Closure $resourceHandle,
        mixed $value,
        mixed $search = null,
        ?Closure $resourceGenerator = null,
    )
    {
        $filter = [
            'type' => [
                'field' => 'type',
                'type' => $type,
                'value' => $search ?? $value
            ]
        ];

        $createResources = $resourceGenerator ?? function ($type) {
            Example::factory($this->faker->numberBetween(10, 15))->create();
            Example::factory($this->faker->numberBetween(16, 25), ['type' => $type])->create();

            return Example::query();
        };

        $data = $createResources($value);

        $engine = $this->generateEngine(['data' => $data]);
        $resources = $resourceHandle($data);

        $this->assertEquals($resources->count(), $engine->filters($filter)->count());
    }

    /**
     * @param array $mocks
     * @return Base
     * @throws ReflectionException
     * @throws Exception
     */
    protected function instantiateEngine(array $mocks): Base
    {
        if (!isset($this->engine)) {
            throw new Exception("Engine property not configured");
        }

        $setting = $this->engine;

        /** @var Base $instance */
        $instance = (new ReflectionClass($setting['engine']))->newInstanceArgs($mocks);

        return $instance;
    }

    /**
     * @param array $parameters
     * @param array $setting
     * @param array|null $arguments
     * @return array
     * @throws ReflectionException
     */
    protected function resolveEngineDependencies(array $parameters, array $setting, ?array $arguments = null): array
    {
        $results = [];

        foreach ($parameters as $parameter) {
            $class = Util::getParameterClassName($parameter);
            $name = $parameter->getName();

            $dependency = new ReflectionClass($class);

            $results[] = match (true) {
                $dependency->implementsInterface(Orchestration::class) === true => $this->generateOrchestratorInstance($setting),
                default => $arguments[$name] ?? Mockery::mock($class)
            };
        }
        return $results;
    }
}