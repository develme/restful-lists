<?php


/**
 * Polyfill for array_is_list, which is available in PHP 8.1
 */

use Illuminate\Http\Response;

if (!function_exists('array_is_list')) {
    function array_is_list(array $array): bool
    {
        if (empty($array)) {
            return true;
        }

        $current_key = 0;
        foreach ($array as $key => $noop) {
            if ($key !== $current_key) {
                return false;
            }
            ++$current_key;
        }

        return true;
    }
}

if (!function_exists('response') && !class_exists(\Illuminate\Routing\ResponseFactory::class)) {
    function response($content = '', $status = 200, array $headers = [])
    {
        $factory = new \Tests\Response\ResponseFactory;

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}