<?php


namespace Tests\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

trait WithHttpRequests
{
    protected function instantiateRequest($url, $method = 'GET', $data = []): Request
    {
        $request = SymfonyRequest::create(
            $url, $method, [], [], [], [], ''
        );

        return Request::createFromBase($request);
    }



    /**
     * @param array $params
     * @return string
     */
    protected function constructUrlFromParams(array $params): string
    {
        return "https://www.develme.com/example?" . http_build_query($params, "", "&");
    }
}