<?php


namespace Tests\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Tests\Models\Example;
use Tests\Resources\ExampleResource;

trait WithHttpRequests
{
    protected function instantiateRequest($url, $method = 'GET', $data = []): Request
    {
        $request = SymfonyRequest::create($url, $method, [], [], [], [], '');
        $result = Request::createFromBase($request);

        $this->container->instance('request', $result);

        return $result;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function constructUrlFromParams(array $params): string
    {
        return "https://www.develme.com/example?" . http_build_query($params, "", "&");
    }

    protected function parseResponse(Response $response): array
    {
        return ['headers' => $response->headers, 'content' => $response->getContent()];
    }

    protected function getJsonFromResponse(Response $response): string
    {
        return data_get($this->parseResponse($response), 'content', '{}');
    }
}