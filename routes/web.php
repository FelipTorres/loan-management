<?php

$router->get('/api-docs', 'Swagger\SwaggerController@docs');

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->post('/spreadsheet', 'User\UserController@spreadsheet');
    $router->get('/spreadsheet', 'User\UserController@createSpreadsheet');
    $router->get('/', 'User\UserController@all');
    $router->get('/{uuid}', 'User\UserController@findById');
});
