<?php


namespace Tests\Response;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseFactory implements \Illuminate\Contracts\Routing\ResponseFactory
{

    public function make($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }

    public function noContent($status = 204, array $headers = [])
    {
        return $this->make('', $status, $headers);
    }

    public function view($view, $data = [], $status = 200, array $headers = [])
    {
        return 'No View Support';
    }

    public function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }

    public function jsonp($callback, $data = [], $status = 200, array $headers = [], $options = 0)
    {
        return $this->json($data, $status, $headers, $options)->setCallback($callback);
    }

    public function stream($callback, $status = 200, array $headers = [])
    {
        return new StreamedResponse($callback, $status, $headers);
    }

    public function streamDownload($callback, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $response = new StreamedResponse($callback, 200, $headers);

        if (! is_null($name)) {
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                $disposition,
                $name,
                $this->fallbackName($name)
            ));
        }

        return $response;
    }

    public function download($file, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $response = new BinaryFileResponse($file, 200, $headers, true, $disposition);

        if (! is_null($name)) {
            return $response->setContentDisposition($disposition, $name, $this->fallbackName($name));
        }

        return $response;
    }

    /**
     * Convert the string to ASCII characters that are equivalent to the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function fallbackName($name)
    {
        return str_replace('%', '', Str::ascii($name));
    }

    public function file($file, array $headers = [])
    {
        return new BinaryFileResponse($file, 200, $headers);
    }

    public function redirectTo($path, $status = 302, $headers = [], $secure = null)
    {
        return new RedirectResponse($path, $status, $headers);
    }

    public function redirectToRoute($route, $parameters = [], $status = 302, $headers = [])
    {
        return new RedirectResponse('no-route-support', $status, $headers);
    }

    public function redirectToAction($action, $parameters = [], $status = 302, $headers = [])
    {
        return new RedirectResponse('no-action-support', $status, $headers);
        // TODO: Implement redirectToAction() method.
    }

    public function redirectGuest($path, $status = 302, $headers = [], $secure = null)
    {
        return new RedirectResponse('no-guest-support', $status, $headers);
        // TODO: Implement redirectGuest() method.
    }

    public function redirectToIntended($default = '/', $status = 302, $headers = [], $secure = null)
    {
        return new RedirectResponse('no-intended-support', $status, $headers);
        // TODO: Implement redirectToIntended() method.
    }
}