<?php


namespace Tests\Traits;


trait WithJsonTesting
{

    protected function assertJsonPath(string $json, string $path, mixed $expect)
    {
        $this->assertEquals($expect, $this->json($json, $path), "Not equal at path: $path");
    }

    protected function json(string $json, $key = null): mixed
    {
        $decoded = json_decode($json);

        return data_get($decoded, $key);
    }
}