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
     * 获取GET参数
     * @param null $key
     * @param null $default
     * @return null
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * 获取POST参数
     * @param null $key
     * @param null $default
     * @return null
     */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * 获取全部参数
     * @return array|mixed|null
     */
    public function all()
    {
        if ($this->isJson()) {
            return $this->json() + $this->get();
        }

        return $this->isGet() ? $this->get() : $this->get() + $this->post();
    }

    /**
     * 获取任一输入参数
     * @param null $key
     * @param null $default
     * @return array|mixed|null
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
     * 获取json
     * @param null $key
     * @param null $default
     * @return array|mixed|null
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
     * 判断当前请求是否是传输json
     * @return bool
     */
    public function isJson()
    {
        return mb_strpos($_SERVER['CONTENT_TYPE'], 'json') !== false;
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
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

        return (string) $pathInfo;
    }
}
