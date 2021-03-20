<?php


namespace Tests\Feature\Model;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
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

        $resource = Example::orderBy('created_at', 'desc')->first();
        $response = $this->fetchResponseWithParams();

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
        ];

        $resources = Example::where('type', 'Open')->orderBy('created_at', 'desc')->get();
        $response = $this->fetchResponseWithParams($params);

        $this->assertJsonPath($response, "data.0.name", $resources->first()->name);
        $this->assertJsonPath($response, "meta.total", Example::count());
        $this->assertJsonPath($response, "meta.count", $resources->count());
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
        $response = $this->fetchResponseWithParams($params);

        $this->assertJsonPath($response, "data.0.name", $resource->name);
    }

    /**
     * @test
     */
    public function it_parses_pagination_requests_params()
    {
        Example::factory($this->faker->numberBetween(20, 30))->closed()->create();
        Example::factory(100, ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $params = [
            'pagination' => [
                'page' => 4,
                'size' => 20,
            ],
        ];

        $resources = Example::orderBy('created_at', 'desc')->skip(60)->limit(20)->get();
        $response = $this->fetchResponseWithParams($params);

        $this->assertJsonPath($response, "data.0.name", $resources->first()->name);
        $this->assertJsonPath($response, "data.19.name", $resources->last()->name);
        $this->assertJsonPath($response, "meta.total", Example::count());
        $this->assertJsonPath($response, "meta.count", $resources->count());
        $this->assertJsonPath($response, "meta.count", $params['pagination']['size']);
    }

    /**
     * @test
     */
    public function it_provides_pagination_links_for_next_page()
    {
        Example::factory($this->faker->numberBetween(50, 100))->closed()->create();
        Example::factory($this->faker->numberBetween(50, 100), ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $page = 1;
        $size = 20;
        $params = [
            'pagination' => [
                'page' => $page,
                'size' => $size,
            ],
        ];

        $total = Example::count();
        $request = $this->instantiateRequest($this->constructUrlFromParams($params));

        do {
            $service = new Service($request);
            $service->model(Example::query());

            $response = $this->getJsonFromResponse($service->json());

            $this->assertJsonNotEmpty($response, "meta.pagination.current");
            $this->assertJsonPath($response, "meta.total", $total);

            $next = $this->json($response, 'meta.pagination.urls.next');

            if (empty($next)) {
                break;
            } else {
                $this->assertJsonPath($response, 'meta.count', $size);
                $this->assertJsonPath($response, 'meta.total', $total);
            }

            $request = $this->instantiateRequest($next);
            $page++;
        } while($page <= $this->json($response, 'meta.pagination.total', 0));
    }

    /**
     * @test
     */
    public function it_utilizes_json_resources()
    {
        Example::factory($this->faker->numberBetween(20, 30))->closed()->create();
        Example::factory($this->faker->numberBetween(20, 30), ['created_at' => Carbon::now()->subDays(5)])->open()->create();

        $resources = Example::orderBy('created_at', 'desc')->first();
        $response = $this->fetchResponseWithParams(resource: ExampleResource::class);

        $this->assertJsonPath($response, "data.0.name", $resources->first()->name);

        // Json resource should not contain a person's email
        $this->assertJsonEmpty($response, "data.0.email");
    }

    protected function fetchResponseWithParams(?array $params = [], JsonResource|string|null $resource = null): string
    {
        $request = $this->instantiateRequest($this->constructUrlFromParams($params));
        $service = new Service($request);

        $resource and $service->resource($resource);

        return $this->getJsonFromResponse($service->model(new Example)->json());
    }
}