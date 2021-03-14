<?php


namespace Tests\Feature\Collection\Engine;

use Illuminate\Support\Collection;
use ReflectionException;
use Tests\Contracts\ChecksOrderSupport;
use Tests\Feature\Collection\TestCase;
use Tests\Models\Example;

/**
 * Class OrderTest
 * @package Tests\Feature\Collection\Engine
 * @group Collection
 * @group Order
 * @group Engine
 */
class OrderTest extends TestCase
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_simplified_ordering(): void
    {
        $orders = ['type', 'name' => 'desc'];

        $all = new Collection(Example::factory($this->faker->numberBetween(10, 15))->make()->toArray());

        $resources = $all->sortBy('type')->sortByDesc('name');

        $engine = $this->generateEngine(['data' => $all]);
        $results = $engine->orders($orders)->go();

        $this->assertEquals($resources->first()['type'], $results->first()['type']);
        $this->assertEquals($resources->last()['type'], $results->last()['type']);
        $this->assertEquals($resources->first()['name'], $results->first()['name']);
        $this->assertEquals($resources->last()['name'], $results->last()['name']);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function it_has_complex_ordering(): void
    {
        $orders = [
            'type' => [
                'field' => 'type',
                'direction' => 'desc',
            ],
            'name' => [
                'field' => 'name',
                'direction' => 'asc'
            ]
        ];
        $all = new Collection(Example::factory($this->faker->numberBetween(10, 15))->make()->toArray());

        $resources = $all->sortByDesc('type')->sortBy('name');

        $engine = $this->generateEngine(['data' => $all]);
        $results = $engine->orders($orders)->go();

        $this->assertEquals($resources->first()['type'], $results->first()['type']);
        $this->assertEquals($resources->last()['type'], $results->last()['type']);
        $this->assertEquals($resources->first()['name'], $results->first()['name']);
        $this->assertEquals($resources->last()['name'], $results->last()['name']);
    }
}