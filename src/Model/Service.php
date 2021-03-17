<?php


namespace DevelMe\RestfulList\Model;

use DevelMe\RestfulList\Engines\Base;
use DevelMe\RestfulList\Engines\Model as Engine;
use DevelMe\RestfulList\Model\Orchestration\Orchestrator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Service
{
    protected Model|Builder $model;

    protected static bool $sortNewestToOldest = true;

    protected static string $sortColumn = 'created_at';

    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function model(Model|Builder $model): static
    {
        $this->model = $model;

        return $this;
    }


    public function json(): string
    {
        $engine = $this->prepareEngine();

        return json_encode([
            'resources' => $engine->go()->toArray(),
            'total' => $engine->total(),
            'count' => $engine->count(),
        ]);
    }

    protected function prepareEngine(): Base
    {
        $builder = $this->fetchBuilder();

        $orchestrator = new Orchestrator;
        $orchestrator->register();

        $engine = new Engine($builder, $orchestrator);

        $engine->orders($this->request->get('orders', []));

        $engine->filters($this->request->get('filters', []));
        $engine->pagination($this->request->get('pagination', []));

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
}