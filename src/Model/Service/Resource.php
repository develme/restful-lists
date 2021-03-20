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

        $result = [
            "data" => $this->collection,
            "meta" => [
                "count" => $this->engine->count(),
                "total" => $this->engine->total(),
            ]
        ];

        if ($request->has('pagination')) {
            $result['meta']['pagination'] = $this->paginationMeta($request);
        }

        return $result;
    }

    private function paginationMeta(\Illuminate\Http\Request $request): array
    {
        $params = $request->all();
        $pagination = data_get($params, 'pagination', []);
        $page = (int) data_get($pagination, 'page', 1);
        $size = (int) data_get($pagination, 'size');
        $total = $this->engine->total();
        $pages = ceil($total / $size);

        $meta = [
            'previous' => $page > 1 ? $page - 1 : null,
            'current' => $page,
            'next' => $page < $pages ? $page + 1 : null,
            'size' => $size,
            'total' => $pages,
            'urls' => [
                'previous' => null,
                'current' => $request->fullUrl(),
                'next' => null,
            ],
        ];

        if ($page < $pages) {
            $meta['urls']['next'] = $request->url() . '?' . http_build_query(array_merge($params, ['pagination' => array_merge($pagination, ['page' => $page + 1])]), "", "&");
        }

        if ($page > 1) {
            $meta['urls']['previous'] = $request->url() . '?' . http_build_query(array_merge($params, ['pagination' => array_merge($pagination, ['page' => $page - 1])]), "", "&");
        }

        return $meta;
    }
}