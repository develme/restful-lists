<?php


namespace Tests\Feature\Model\Engine;

use ReflectionException;
use Tests\Feature\Model\TestCase;
use Tests\Models\Example;

/**
 * Class OrderTest
 * @package Tests\Feature\Model\Engine
 * @group Model
 * @group Order
 * @group Engine
 */
class OrderTest extends TestCase
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_simplified_ordering()
    {
        $orders = ['type', 'status' => 'desc'];

        Example::factory($this->faker->numberBetween(10, 15))->create();

        $engine = $this->generateEngine(['data' => Example::query()]);
        $resources = Example::orderBy('type')->orderBy('status', 'desc')->get();
        $results = $engine->orders($orders)->go();


        $this->assertEquals($resources->first()->name, $results->first()->name);
        $this->assertEquals($resources->last()->name, $results->last()->name);
    }
    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_complex_ordering()
    {
        $orders = [
            'type' => [
                'field' => 'type',
                'direction' => 'desc',
            ],
            'status' => [
                'field' => 'status',
                'direction' => 'asc'
            ]
        ];

        Example::factory($this->faker->numberBetween(10, 15))->create();

        $engine = $this->generateEngine(['data' => Example::query()]);
        $resources = Example::orderBy('type', 'desc')->orderBy('status')->get();
        $results = $engine->orders($orders)->go();

        $this->assertEquals($resources->first()->type, $results->first()->type);
        $this->assertEquals($resources->last()->status, $results->last()->stauts);
    }
}