<?php


namespace DevelMe\RestfulList\Model;

use DevelMe\RestfulList\Engines\Base;
use DevelMe\RestfulList\Engines\Model as Engine;
use DevelMe\RestfulList\Model\Orchestration\Orchestrator;
use DevelMe\RestfulList\Model\Service\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class Service
{
    protected Model|Builder $model;

    protected static bool $sortNewestToOldest = true;

    protected static string $sortColumn = 'created_at';

    protected Request $request;

    protected string|JsonResource $resource;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function model(Model|Builder $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function resource(string|JsonResource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }


    public function json(): Response
    {
        $engine = $this->prepareEngine();

        $results = $engine->go();

        if (!empty($this->resource)) {
            $resource = new Resource($this->resource::collection($results));
        } else {
            $resource = new Resource($results);
        }

        return $resource->setEngine($engine)->response();
    }

    protected function prepareEngine(): Base
    {
        $builder = $this->fetchBuilder();

        $orchestrator = new Orchestrator;
        $orchestrator->register();

        $engine = new Engine($builder, $orchestrator);

        $engine->orders($this->request->get('orders', []));

        $engine->filters($this->request->get('filters', []));

        $this->preparePagination($engine);

        return $engine;
    }

    /**
     * @return Builder
     */
    protected function fetchBuilder(): Builder
    {
        $builder = $this->model instanceof Model ? $this->model->query() : $this->model;

        if (!$this->request->has('orders') && static::$sortNewestToOldest) {
            $builder->orderBy(static::$sortColumn, 'desc');
        }

        return $builder;
    }

    /**
     * @param Engine $engine
     */
    protected function preparePagination(Engine $engine): void
    {
        $pagination = $this->request->get('pagination', []);
        $page = data_get($pagination, 'page', 1);
        $size = data_get($pagination, 'size');

        if (!empty($size)) {
            $start = ((int)$page * (int)$size) - (int)$size;
            $end = $start + $size;

            $engine->pagination(compact('start', 'end'));
        }
    }
}