<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/8/7 21:21
 */

return [
    'env' => 'local',
    'debug' => true,
    'timezone' => 'UTC',

    'log' => [
        'path' => 'logs',
        'name' => 'app.log',
        'maxFiles' => 100,
        'level' => \Monolog\Logger::DEBUG
    ],
];