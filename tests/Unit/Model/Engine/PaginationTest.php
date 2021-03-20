<?php


namespace Tests\Unit\Model\Engine;

use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Tests\Models\Example;
use Tests\Traits\WithFaker;
use Tests\Unit\Model\TestCase;

class PaginationTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_has_simple_pagination()
    {
        $pagination = [50, 100];

        $resources = Example::factory(50)->make();
        $total = $this->faker->numberBetween(400, 500);

        $mocks = $this->generateEngineMock(function ($mocks) use ($resources, $total, $pagination) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('skip')->with($pagination[0])->once()->andReturn($mock);
                    $mock->shouldReceive('limit')->with($pagination[1] - $pagination[0])->once()->andReturn($mock);
                    $this->mockBuilderWithResources($mock, $resources, [$total, $resources->count()]);
                }
            }

            return $mocks;
        });

        $this->checkPaginationAgainstEngine(
            pagination: $pagination,
            mocks: $mocks,
            method: function ($engine, \Tests\TestCase $tester) use ($resources, $total) {
                $tester->assertEquals($resources->count(), $engine->count());
                $tester->assertEquals($total, $engine->total());
            }
        );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_has_complex_pagination()
    {
        $pagination = ['start' => 0, 'end' => 100];

        $resources = Example::factory(100)->make();
        $total = 400;

        $mocks = $this->generateEngineMock(function ($mocks) use ($resources, $total, $pagination) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('skip')->with($pagination['start'])->once()->andReturn($mock);
                    $mock->shouldReceive('limit')->with($pagination['end'] - $pagination['start'])->once()->andReturn($mock);
                    $this->mockBuilderWithResources($mock, $resources, [$total, $resources->count()]);
                }
            }

            return $mocks;
        });

        $this->checkPaginationAgainstEngine(
            pagination: $pagination,
            mocks: $mocks,
            method: function ($engine, \Tests\TestCase $tester) use ($resources, $total) {
                $tester->assertEquals($resources->count(), $engine->count());
                $tester->assertEquals($total, $engine->total());
            }
        );
    }
}