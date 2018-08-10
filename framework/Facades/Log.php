<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/9 21:42
 */

namespace Mocha\Framework\Facades;

/**
 * Class Log
 * @package Mocha\Framework\Facades
 * @see \Monolog\Logger
 * @method static debug($message, array $context = [])
 * @method static info($message, array $context = [])
 * @method static notice($message, array $context = [])
 * @method static warning($message, array $context = [])
 * @method static error($message, array $context = [])
 * @method static critical($message, array $context = [])
 * @method static alert($message, array $context = [])
 * @method static emergency($message, array $context = [])
 */
class Log extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
