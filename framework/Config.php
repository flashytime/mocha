<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/7 10:15
 */

namespace Mocha\Framework;

/**
 * Class Config
 * @package Mocha\Framework
 */
class Config
{
    /**
     * All of the configuration items.
     * @var array
     */
    protected $items = [];

    /**
     * Config constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Set a given configuration value.
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Get the specified configuration value.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $array = $this->items;
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $item) {
            if (array_key_exists($item, $array)) {
                $array = $array[$item];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Determine if the given configuration value exists.
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $array = $this->items;
        if (is_null($key) || empty($array)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $item) {
            if (array_key_exists($item, $array)) {
                $array = $array[$item];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all of the configuration items.
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
}
