<?php


namespace Tests\Feature\Model;

use Carbon\Carbon;
use Tests\Models\Example;
use DevelMe\RestfulList\Model\Service;
use Tests\Resources\ExampleResource;
use Tests\Traits\WithHttpRequests;
use Tests\Traits\WithJsonTesting;

/**
 * Class ServiceTest
 * @package Tests\Feature\Collection
 * @group Collection
 * @group Service
 */
class ServiceTest extends TestCase
{
    use WithJsonTesting, WithHttpRequests;

    /**
     * @test
     */
    public function it_returns_all()
    {
        Example::factory($this->faker->numberBetween(5, 15), ['created_at' => Carbon::now()->subDays(5)])->closed()->create();
        Example::factory($this->faker->numberBetween(5, 15))->open()->create();

        $service = new Service($this->instantiateRequest(""));

        $resource = Example::orderBy('created_at', 'desc')->first();
        $response = $this->parseResponse($service->model(new Example)->json())['content'];

        $this->assertJsonPath($response, "data.0.name", $resource->name);
    }

    /**
     * @test
     */
    public function it_parses_filter_requests_params()
    {
        Example::factory($this->faker->numberBetween(10, 20))->closed()->create();
        Example::factory($this->faker->numberBetween(10, 20), ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $params = [
            'filters' => [
                'type' => [
                    'field' => 'type',
                    'type' => 'equals',
                    'value' => 'Open'
                ],
            ],
//            'orders' => [
//                'name' => [
//                    'field' => 'name',
//                    'direction' => 'desc',
//                ],
//            ],
//            'pagination' => [
//                'start' => 100,
//                'end' => 200,
//            ],
        ];

        $resources = Example::where('type', 'Open')->orderBy('created_at', 'desc')->get();
        $request = $this->instantiateRequest($this->constructUrlFromParams($params));
        $service = new Service($request);

        $response = $this->parseResponse($service->model(new Example)->json())['content'];

        $this->assertJsonPath($response, "data.0.name", $resources->first()->name);
        $this->assertJsonPath($response, "total", Example::count());
        $this->assertJsonPath($response, "count", $resources->count());

    }

    /**
     * @test
     */
    public function it_parses_order_requests_params()
    {
        Example::factory($this->faker->numberBetween(10, 20))->closed()->create();
        Example::factory($this->faker->numberBetween(10, 20), ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $params = [
            'orders' => [
                'name' => [
                    'field' => 'name',
                    'direction' => 'desc',
                ],
            ],
        ];

        $resource = Example::orderBy('name', 'desc')->first();
        $request = $this->instantiateRequest($this->constructUrlFromParams($params));
        $service = new Service($request);
        $response = $this->parseResponse($service->model(new Example)->json())['content'];

        $this->assertJsonPath($response, "data.0.name", $resource->name);

    }

    /**
     * @test
     */
    public function it_parses_pagination_requests_params()
    {
        Example::factory($this->faker->numberBetween(20, 30))->closed()->create();
        Example::factory($this->faker->numberBetween(20, 30), ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $params = [
            'pagination' => [
                'start' => 10,
                'end' => 20,
            ],
        ];

        $resources = Example::orderBy('created_at', 'desc')->skip(10)->limit(20)->get();
        $request = $this->instantiateRequest($this->constructUrlFromParams($params));
        $service = new Service($request);

        $response = $this->parseResponse($service->model(new Example)->json())['content'];

        $this->assertJsonPath($response, "data.0.name", $resources->first()->name);
        $this->assertJsonPath($response, "total", Example::count());
        $this->assertJsonPath($response, "count", $resources->count());

    }

    /**
     * @test
     */
    public function it_utilizes_json_resources()
    {
        Example::factory($this->faker->numberBetween(20, 30))->closed()->create();
        Example::factory($this->faker->numberBetween(20, 30), ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $request = $this->instantiateRequest($this->constructUrlFromParams([]));
        $service = new Service($request);
        $resources = Example::orderBy('created_at', 'desc')->first();

        $response = $this->parseResponse($service->model(Example::query())->resource(ExampleResource::class)->json())['content'];

        $this->assertJsonPath($response, "data.0.name", $resources->first()->name);
        $this->assertJsonEmpty($response, "data.0.email");

    }
}