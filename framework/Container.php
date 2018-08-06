<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 14:57
 */

namespace Mocha\Framework;

use Closure;
use ReflectionClass;

/**
 * Class Container
 * @package Mocha\Framework
 */
class Container
{
    /**
     * The current globally available container.
     * @var static
     */
    protected static $instance;

    /**
     * The container's bindings.
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's singleton instances.
     * @var array
     */
    protected $singletons = [];

    /**
     * Cached ReflectionClass objects indexed by class/interface names.
     * @var array
     */
    private $reflections = [];

    /**
     * Cached dependencies indexed by class/interface names.
     * @var array
     */
    private $dependencies = [];

    /**
     * Set the shared instance of the container.
     * @param \Mocha\Framework\Container|null $container
     * @return static
     */
    public static function setInstance(Container $container = null)
    {
        return static::$instance = $container;
    }

    /**
     * Get the globally available instance of the container.
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Register a binding in the container.
     * @param string $abstract
     * @param \Closure|string|null $concrete
     * @param bool $singleton
     */
    public function bind($abstract, $concrete = null, $singleton = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        if ($singleton) {
            $this->singletons[$abstract] = null;
        } else {
            unset($this->singletons[$abstract]);
        }

        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Register a singleton binding in the container.
     * @param string $abstract
     * @param \Closure|string|null $concrete
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Resolve the given type from the container.
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        if (isset($this->singletons[$abstract])) {
            return $this->singletons[$abstract];
        }

        $concrete = isset($this->bindings[$abstract]) ? $this->bindings[$abstract] : $abstract;

        if ($concrete === $abstract || $concrete instanceof Closure) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete, $parameters);
        }

        if (array_key_exists($abstract, $this->singletons)) {
            $this->singletons[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Instantiate a concrete instance of the given type.
     * @param string $concrete
     * @param array $parameters
     * @return mixed
     */
    public function build($concrete, array $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        $args = [];
        list($reflection, $dependencies) = $this->getDependencies($concrete);
        foreach ($dependencies as $index => $dependency) {
            $args[$dependency] = $this->make($dependency);
        }

        return $reflection->newInstanceArgs(array_merge($args, $parameters));
    }

    /**
     * Get the Closure to be used when building a type.
     * @param string $abstract
     * @param string $concrete
     * @return Closure
     */
    protected function getClosure($abstract, $concrete)
    {
        return function ($container, ...$parameters) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';

            return $container->$method($concrete, $parameters);
        };
    }

    /**
     * Returns the reflections and dependencies of the specified class.
     * @param string $class
     * @return array
     * @throws \Exception
     */
    protected function getDependencies($class)
    {
        if (isset($this->reflections[$class])) {
            return [$this->reflections[$class], $this->dependencies[$class]];
        }

        $dependencies = [];
        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new \Exception(sprintf('"%s" is not instantiable', $class));
        }

        $constructor = $reflection->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                if ($parameter->getClass()) {
                    $dependencies[] = $parameter->getClass()->getName();
                }
            }
        }
        $this->reflections[$class] = $reflection;
        $this->dependencies[$class] = $dependencies;

        return [$reflection, $dependencies];
    }
}
