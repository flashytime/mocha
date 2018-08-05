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
     * @var static
     */
    protected static $instance;

    /**
     * 容器的 bindings
     * @var array
     */
    protected $bindings = [];

    /**
     * 存储单例标记和单例
     * @var array
     */
    protected $singletons = [];

    /**
     * 缓存ReflectionClass
     * @var array
     */
    private $reflections = [];

    /**
     * 缓存依赖
     * @var array
     */
    private $dependencies = [];

    /**
     * 设置container的实例
     * @param \Mocha\Framework\Container|null $container
     * @return static
     */
    public static function setInstance(Container $container = null)
    {
        return static::$instance = $container;
    }

    /**
     * 获取container的实例
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
     * 在 Container 注册一个 binding
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
     * 在 Container 上注册一个单例的 binding
     * @param string $abstract
     * @param \Closure|string|null $concrete
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * 根据容器中的绑定，给出 $abstract 的实例
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
     * 构建 $concrete 对应的对象
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
     * 创建关于 $abstract 和 $concrete 的闭包
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
     * 获取类的反射和依赖
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
