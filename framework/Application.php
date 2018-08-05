<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:15
 */

namespace Mocha\Framework;

/**
 * Class Application
 * @package Mocha\Framework
 */
class Application extends Container
{
    /**
     * @var Router
     */
    public $router;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * Application constructor.
     * @param null $basePath
     */
    public function __construct($basePath = null)
    {
        static::setInstance($this);
        $this->basePath = $basePath;
        $this->router = new Router($this);
    }

    /**
     * @return string
     */
    public function version()
    {
        return 'Mocha Framework (v1.0.0)';
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'app';
    }

    /**
     * 执行App并返回response
     */
    public function run()
    {
        $request = $this->make(Request::class);
        $response = $this->router->dispatch($request);
        $response->send();
    }
}
