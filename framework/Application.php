<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 15:15
 */

namespace Mocha\Framework;

use Mocha\Framework\Exception\ExceptionHandler;
use Mocha\Framework\Facades\Facade;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

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
        $this->bootstrap();
        $this->registerExceptionHandler();
    }

    /**
     * Bootstrap the application and register the core instances.
     * @return void
     */
    protected function bootstrap()
    {
        $this->router = new Router($this);

        $this->singleton('config', function () {
            return new Config();
        });

        $this->singleton('request', function () {
            return new Request();
        });

        $this->singleton('log', function () {
            $logConfig = config('app.log');
            $filename = $this->runtimePath() . '/' . trim($logConfig['path'], '/') . '/' . $logConfig['name'];

            return new Logger('mocha',
                [new RotatingFileHandler($filename, $logConfig['maxFiles'], $logConfig['level'])]);
        });

        Facade::setFacadeApp($this);
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
     * Get the path to the runtime directory.
     * @return string
     */
    public function runtimePath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'runtime';
    }

    /**
     * Load a configuration file into the application.
     * @param string $name
     * @return void
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
     * @return void
     */
    public function run()
    {
        $request = $this->make('request');
        $response = $this->router->dispatch($request);
        $response->send();
    }

    /**
     * Register the exception handler and error handler for the application.
     * @return void
     */
    protected function registerExceptionHandler()
    {
        error_reporting(-1);

        set_error_handler(function ($level, $message, $file = '', $line = 0) {
            if (error_reporting() & $level) {
                throw new \ErrorException($message, 0, $level, $file, $line);
            }
        });

        set_exception_handler(function ($e) {
            $this->handleException($e)->send();
        });
    }

    /**
     * Handle the exception and return the response.
     * @param \Exception $e
     * @return \Mocha\Framework\Response
     */
    public function handleException($e)
    {
        $handler = $this->make(ExceptionHandler::class);
        $handler->report($e);

        return $handler->render($this->make('request'), $e);
    }
}
