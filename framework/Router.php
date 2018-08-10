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
     * The application instance.
     * @var Application
     */
    public $app;

    /**
     * All of the routes waiting to be registered.
     * @var array
     */
    protected $routes = [];

    /**
     * The route group.
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
     * Register a route group
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
     * Register a route dynamically
     * @param string $method
     * @param array $arguments
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
     * Add a route.
     * @param string $method
     * @param string $uri
     * @param string $action
     * @return void
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
     * Dispatch the incoming request and find route.
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
            return $this->app->handleException($e);
        }
    }

    /**
     * Call the given Closure / "class@method"
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
            $controller = $this->app->make($controller);
            if (!method_exists($controller, $action)) {
                throw new \BadMethodCallException("Method [$action] not found.", 500);
            }
            $callback = [$controller, $action];
        }

        return call_user_func($callback);
    }
}
