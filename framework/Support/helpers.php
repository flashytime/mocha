<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/2 21:49
 */

use \Mocha\Framework\Container;
use \Mocha\Framework\View;
use \Mocha\Framework\Config;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
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

if (!function_exists('request')) {
    /**
     * Get the Request instance
     * @return \Mocha\Framework\Request
     */
    function request()
    {
        return app('request');
    }
}

if (!function_exists('config')) {
    /**
     * Get the specified configuration value.
     * @param array|string $key
     * @param mixed $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        return app('config')->get($key, $default);
    }
}

if (!function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
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
     * Get the base path for the application.
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
     * Get the path to the application "app" directory.
     * @param string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app()->path() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('array_get')) {
    /**
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}