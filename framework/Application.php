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
     * All of the loaded configuration files.
     * @var array
     */
    protected $loadedConfigurations = [];

    /**
     * Application constructor.
     * @param null $basePath
     */
    public function __construct($basePath = null)
    {
        static::setInstance($this);
        $this->basePath = $basePath;
        $this->router = new Router($this);
        $this->singleton('config', function () {
            return new Config();
        });
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
     * Load a configuration file into the application.
     * @param string $name
     */
    public function configure($name)
    {
        if (isset($this->loadedConfigurations[$name])) {
            return;
        }
        $this->loadedConfigurations[$name] = true;

        $path = $this->basePath() . '/config/' . $name . '.php';
        if (file_exists($path)) {
            $this->make('config')->set($name, require $path);
        }
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
