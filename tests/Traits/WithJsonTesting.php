<?php


namespace Tests\Traits;


trait WithJsonTesting
{

    protected function assertJsonPath(string $json, string $path, mixed $expect)
    {
        $this->assertEquals($expect, $this->json($json, $path), "Not equal at path: $path");
    }

    protected function assertJsonEmpty(string $json, string $path)
    {
        $this->assertEmpty($this->json($json, $path), "Not empty at path: $path");
    }

    protected function assertJsonNotEmpty(string $json, string $path)
    {
        $this->assertNotEmpty($this->json($json, $path), "Empty at path: $path");
    }


    protected function json(string $json, $key = null, $default = null): mixed
    {
        $decoded = json_decode($json);

        return data_get($decoded, $key, $default);
    }
}