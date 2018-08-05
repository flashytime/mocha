<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 17:40
 */

namespace Mocha\Tests\Framework;

use Mocha\Framework\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    public $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testBind()
    {
        $this->container->bind('foo', Foo::class);
        $foo = $this->container->make('foo');
        $this->assertInstanceOf(Foo::class, $foo);
        $this->assertInstanceOf(Foo::class, $this->container->make(Foo::class));
    }

    public function testSingleton()
    {
        $this->container->singleton('foo', Foo::class);
        $first = $this->container->make('foo');
        $second = $this->container->make('foo');
        $this->assertSame($first, $second);

        $this->container->bind('foo', Foo::class);
        $first = $this->container->make('foo');
        $second = $this->container->make('foo');
        $this->assertNotSame($first, $second);
    }

    public function testDependencyInjection()
    {
        $this->container->bind(FooInterface::class, Foo::class);
        $bar = $this->container->make(Bar::class);
        $this->assertInstanceOf(Bar::class, $bar);
        $this->assertInstanceOf(FooInterface::class, $bar->getFoo());
    }
}

//tests classes
interface FooInterface
{

}

class Foo implements FooInterface
{
    private $name;
    private $age;

    public function __construct($name = '', $age = 0)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function getAge()
    {
        return $this->age;
    }
}

class Bar
{
    public $foo;

    public function __construct(FooInterface $foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}