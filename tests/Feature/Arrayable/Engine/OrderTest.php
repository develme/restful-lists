<?php


namespace Tests\Feature\Arrayable\Engine;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Tests\Feature\Arrayable\TestCase;
use Tests\Models\Example;
use Tests\Traits\WithEloquent;

/**
 * Class OrderTest
 * @package Tests\Feature\Arrayable\Engine
 * @group Arrayable
 * @group Order
 * @group Engine
 */
class OrderTest extends TestCase
{

    /**
     * @test
     */
    public function it_has_simplified_ordering(): void
    {
        $orders = ['type', 'name' => 'desc'];

        Example::factory($this->faker->numberBetween(10, 15))->create();

        $all = Example::all()->toArray();

        $sorted = collect(Example::all()->toArray());
        $sorted = $sorted->sortBy('type')->sortByDesc('name');

        $engine = $this->generateEngine(['data' => $all]);
        $results = $engine->orders($orders)->go();

        $this->assertEquals($sorted->first()['type'], Arr::first($results)['type']);
        $this->assertEquals($sorted->last()['type'], Arr::last($results)['type']);
        $this->assertEquals($sorted->first()['name'], Arr::first($results)['name']);
        $this->assertEquals($sorted->last()['name'], Arr::last($results)['name']);
    }
}