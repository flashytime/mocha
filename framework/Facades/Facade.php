<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/9 18:19
 */

namespace Mocha\Framework\Facades;

use Mocha\Framework\Application;

/**
 * Class Facade
 * @package Mocha\Framework\Facades
 */
abstract class Facade
{
    /**
     * The application instance.
     * @var \Mocha\Framework\Application
     */
    protected static $app;

    /**
     * The resolved object instances.
     * @var array
     */
    protected static $resolvedInstance = [];

    /**
     * Set the application instance.
     * @param \Mocha\Framework\Application $app
     * @return void
     */
    public static function setFacadeApp(Application $app)
    {
        static::$app = $app;
    }

    /**
     * Get the application instance.
     * @return \Mocha\Framework\Application
     */
    public static function getFacadeApp()
    {
        return static::$app;
    }

    /**
     * Get the registered name of the component.
     * @return string
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * Resolve the facade root instance from the container.
     * @param string $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$app->make($name);
    }

    /**
     * Handle dynamic, static calls to the object.
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());
        return $instance->$method(...$arguments);
    }
}
