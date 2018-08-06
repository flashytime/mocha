<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:15
 */

namespace Mocha\Framework;

/**
 * Class Request
 * @package Mocha\Framework
 */
class Request
{
    /**
     * Get one or all of the GET parameters
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * Get one or all of the POST parameters
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * Get all of the input for the request.
     * @return array
     */
    public function all()
    {
        if ($this->isJson()) {
            return $this->json() + $this->get();
        }

        return $this->isGet() ? $this->get() : $this->get() + $this->post();
    }

    /**
     * Retrieve an input item from the request.
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        $all = $this->all();

        if (is_null($key)) {
            return $all;
        }

        return isset($all[$key]) ? $all[$key] : $default;
    }

    /**
     * Get the JSON payload for the request.
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        $json = (array)json_decode(file_get_contents('php://input'), true);

        if (is_null($key)) {
            return $json;
        }

        return isset($json[$key]) ? $json[$key] : $default;
    }

    /**
     * Determine if the request is sending JSON.
     * @return bool
     */
    public function isJson()
    {
        return mb_strpos($_SERVER['CONTENT_TYPE'], 'json') !== false;
    }

    /**
     * Determine if this is a GET request.
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Determine if this is a POST request.
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Determine if the request is the result of an AJAX call.
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Get the request method.
     * @return string
     */
    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get the current path info for the request.
     * @return string
     */
    public function getPathInfo()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
        $pathInfo = substr($requestUri, strlen($baseUrl));
        if ('' !== $baseUrl && (false === $pathInfo || '' === $pathInfo)) {
            return '/';
        }

        return (string)$pathInfo;
    }
}
