<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router){
    return 'Comments Api Challenge version ' . $router->app->version();
});

$router->get('/config', function () use ($router) {
    return \App\Models\ApiSettings::all();
});

$router->group(['prefix'=>'comments'],function() use($router){
    $router->get('','CommentController@getAll');
    $router->post('','CommentController@store');
});

