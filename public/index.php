<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 14:52
 */

require __DIR__ . '/../vendor/autoload.php';

$app = new \Mocha\Framework\Application(realpath(__DIR__ . '/../'));

$app->router->group(['namespace' => 'Mocha\App\Controllers'], function ($router) {
    require __DIR__ . '/../app/routes.php';
});

$app->run();