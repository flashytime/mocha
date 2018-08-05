<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:15
 */

namespace Mocha\Framework;

use Closure;

/**
 * Class Router
 * @method get($uri, $action)
 * @method post($uri, $action)
 * @method put($uri, $action)
 * @method patch($uri, $action)
 * @method delete($uri, $action)
 * @method options($uri, $action)
 * @package Mocha\Framework
 */
class Router
{
    /**
     * @var Application
     */
    public $app;

    /**
     * 路由
     * @var array
     */
    protected $routes = [];

    /**
     * 路由组
     * @var array
     */
    protected $groups = [];

    /**
     * Router constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 设置路由组
     * @param array $attributes
     * @param Closure $callback
     */
    public function group(array $attributes, Closure $callback)
    {
        $group = [];
        if (isset($attributes['namespace'])) {
            $group['namespace'] = $attributes['namespace'];
        }
        if (isset($attributes['prefix'])) {
            $group['prefix'] = $attributes['prefix'];
        }

        if ($group) {
            $this->groups[] = $group;
        }

        call_user_func($callback, $this);

        if ($group) {
            array_pop($this->groups);
        }
    }

    /**
     * 设置各种路由
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'options'])) {
            throw new \BadMethodCallException("Method {$method} not found.");
        }
        $this->addRoute($method, ...$arguments);

        return $this;
    }

    /**
     * 设置路由
     * @param $method
     * @param $uri
     * @param $action
     */
    public function addRoute($method, $uri, $action)
    {
        $namespace = $prefix = '';
        foreach ($this->groups as $group) {
            if (isset($group['namespace'])) {
                $namespace .= $group['namespace'] . '\\';
            }
            if (isset($group['prefix'])) {
                $prefix .= trim($group['prefix'], '/') . '/';
            }
        }

        $method = strtoupper($method);
        $uri = '/' . $prefix . trim($uri, '/');
        $action = is_callable($action) ? $action : $namespace . $action;
        $this->routes[$method . $uri] = ['method' => $method, 'uri' => $uri, 'action' => $action];
    }

    /**
     * 执行路由
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $pathInfo = $request->getPathInfo();
        try {
            if (isset($this->routes[$method . $pathInfo])) {
                return new Response($this->call($this->routes[$method . $pathInfo]['action']));
            }
            throw new \Exception("Route [$pathInfo] not found.", 404);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param callable|string $callback
     * @return mixed
     */
    protected function call($callback)
    {
        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($controller, $action) = explode('@', $callback);
            if (!$action) {
                throw new \InvalidArgumentException('Method not provided.', 500);
            }
            $callback = [$this->app->make($controller), $action];
        }

        return call_user_func($callback);
    }
}
