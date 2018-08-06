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
     * The router instance.
     * @var Router
     */
    public $router;

    /**
     * The base path of the application.
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
     * Get the version of the Framework.
     * @return string
     */
    public function version()
    {
        return 'Mocha Framework (v1.0.0)';
    }

    /**
     * Get the base path for the application.
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Get the path to the application "app" directory.
     * @return string
     */
    public function path()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'app';
    }

    /**
     * Run the application and send the response.
     */
    public function run()
    {
        $request = $this->make(Request::class);
        $response = $this->router->dispatch($request);
        $response->send();
    }
}
