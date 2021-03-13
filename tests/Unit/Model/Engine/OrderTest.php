<?php


namespace Tests\Unit\Model\Engine;

use Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Tests\Models\Example;
use Tests\Traits\WithFaker;
use Tests\Unit\Model\TestCase;

/**
 * Class OrderTest
 * @package Tests\Unit\Model\Engine
 * @group Model
 * @group Engine
 * @group Order
 */
class OrderTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_has_simplified_ordering()
    {
        $orders = ['type', 'status' => 'desc'];
        $resources = Example::factory($this->faker->numberBetween(5, 20))->make();

        $mocks = $this->generateEngineMock(function ($mocks) use ($resources) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('orderBy')->with('type', 'asc')->once();
                    $mock->shouldReceive('orderBy')->with('status', 'desc')->once();
                    $this->mockBuilderWithResources($mock, $resources);
                }
            }

            return $mocks;
        });

        $this->checkOrdersAgainstEngine(
            orders: $orders,
            mocks: $mocks,
            method: fn($engine, \Tests\TestCase $tester) => $tester->assertEquals($resources->count(), $engine->count())
        );
    }

    /**
     * @test
     */
    public function it_has_complex_ordering()
    {
        $orders = [
            'type' => [
                'field' => 'type',
                'direction' => 'asc',
            ],
            'status' => [
                'field' => 'status',
                'direction' => 'desc',
            ]
        ];

        $resources = Example::factory($this->faker->numberBetween(5, 20))->make();

        $mocks = $this->generateEngineMock(function ($mocks) use ($resources) {
            /** @var MockInterface $mock */
            foreach ($mocks as $mock) {
                if ($mock instanceof Builder) {
                    $mock->shouldReceive('orderBy')->with('type', 'asc')->once();
                    $mock->shouldReceive('orderBy')->with('status', 'desc')->once();
                    $this->mockBuilderWithResources($mock, $resources);
                }
            }

            return $mocks;
        });

        $this->checkOrdersAgainstEngine(
            orders: $orders,
            mocks: $mocks,
            method: fn($engine, \Tests\TestCase $tester) => $tester->assertEquals($resources->count(), $engine->count())
        );
    }
}