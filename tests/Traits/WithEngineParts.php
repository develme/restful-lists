<?php


namespace Tests\Traits;


use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Comparator\Registration;
use DevelMe\RestfulList\Contracts\Orders\Arrangement;
use Illuminate\Container\Util;
use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Closure;
use Exception;
use Mockery;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;

trait WithEngineParts
{
    /**
     * @param string $type
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

        $mocks = [];
        $setting = $this->engine;
        $reflection = new ReflectionClass($setting['engine']);

        $parameters = $reflection->getConstructor()?->getParameters();

        foreach ($parameters as $dependency) {
            $name = Util::getParameterClassName($dependency);

            $dependency = new ReflectionClass($name);

            $mocks[] = match (true) {
                $dependency->implementsInterface(Composer::class) === true => $this->generateComposerInstance($setting),
                $dependency->implementsInterface(Arrangement::class) === true => $this->generateArrangementInstance($setting),
                default => Mockery::mock($name)
            };
        }

        return $handle($mocks);
    }

    /**
     * @param $setting
     * @return Composer
     * @throws ReflectionException
     */
    protected function generateComposerInstance($setting): Composer
    {
        $composer = new ReflectionClass($setting['composer']);

        /** @var Composer $instance */
        $instance = $composer->newInstance();

        if ($composer->implementsInterface(Registration::class)) {
            $instance->register();
        }

        return $instance;
    }

    /**
     * @param $setting
     * @return Arrangement
     * @throws ReflectionException
     */
    protected function generateArrangementInstance($setting): Arrangement
    {
        $composer = new ReflectionClass($setting['arrangement']);

        /** @var Arrangement $instance */
        $instance = $composer->newInstance();

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
        $mocks = $this->generateEngineMock(function ($mocks) use ($count, $mockHandle) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mockHandle($mock);
                    $mock->shouldReceive('count')->once()->andReturn($count);
                }
            }

            return $mocks;
        });

        $this->checkFiltersAgainstEngine(
            filters: $filter,
            mocks: $mocks,
            method: fn($engine, TestCase $tester) => $tester->assertEquals($count, $engine->count())
        );
    }

    /**
     * @param array $mocks
     * @return object
     * @throws ReflectionException
     * @throws Exception
     */
    protected function instantiateEngine(array $mocks): object
    {
        if (!isset($this->engine)) {
            throw new Exception("Engine property not configured");
        }

        $setting = $this->engine;

        return (new ReflectionClass($setting['engine']))->newInstanceArgs($mocks);
    }
}