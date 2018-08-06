<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/7/31 14:52
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/example', 'ExampleController@index');
$router->get('/example/test', 'ExampleController@test');