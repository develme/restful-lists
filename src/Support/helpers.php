<?php


/**
 * Polyfill for array_is_list, which is available in PHP 8.1
 */
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