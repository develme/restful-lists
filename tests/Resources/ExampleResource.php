<?php


namespace Tests\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ExampleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}