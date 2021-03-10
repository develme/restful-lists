<?php


namespace Tests\Traits;


use DevelMe\RestfulList\Contracts\Comparator\Composer;
use DevelMe\RestfulList\Contracts\Comparator\Registration;
use Illuminate\Container\Util;
use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Closure;
use Exception;
use Mockery;
use ReflectionClass;
use ReflectionException;

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

    /**
     * @param array $filters
     * @param array $mocks
     * @param Closure|string $method
     * @param int $result
     *
     * @throws ReflectionException
     * @throws Exception
     */
    protected function checkFiltersAgainstEngine(array $filters, array $mocks, Closure|string $method = 'count', $result = 0)
    {
        if (!isset($this->engine)) {
            throw new Exception("Engine property not configured");
        }

        $setting = $this->engine;

        $engine = (new ReflectionClass($setting['engine']))->newInstanceArgs($mocks);

        $engine->filters($filters);

        if ($method instanceof Closure) {
            $method($engine);
        } else {
            $this->assertEquals($result, call_user_func([$engine, $method]));
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
            result: $count
        );
    }
}