<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/2 21:49
 */

use \Mocha\Framework\Container;
use \Mocha\Framework\View;

if (!function_exists('app')) {
    /**
     * @param string $make
     * @param array $parameters
     * @return mixed|\Mocha\Framework\Application
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($make, $parameters);
    }
}

if (!function_exists('view')) {
    /**
     * @param string $view
     * @param array $data
     * @return \Mocha\Framework\View
     */
    function view($view, $data = [])
    {
        return app()->make(View::class, [$view, $data]);
    }
}

if (!function_exists('base_path')) {
    /**
     * @param string $path
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('app_path')) {
    /**
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->path() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}