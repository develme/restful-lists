<?php


namespace DevelMe\RestfulList\Model\Service;


use DevelMe\RestfulList\Engines\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Resource extends ResourceCollection
{
    protected Model $engine;

    public function setEngine(Model $engine): static
    {
        $this->engine = $engine;

        return $this;
    }

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'count' => $this->engine->count(),
            'total' => $this->engine->total(),
        ];
    }
}