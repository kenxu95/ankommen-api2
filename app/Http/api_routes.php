<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');
	$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
	$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');

	$api->group(['middleware' => 'api.auth'], function ($api) {		
    $api->get('user', 'App\Api\V1\Controllers\UserController@show');
  	$api->put('user', 'App\Api\V1\Controllers\UserController@update');

    $api->get('user/image', 'App\Api\V1\Controllers\UserController@showImage');
    $api->post('user/image', 'App\Api\V1\Controllers\UserController@storeImage');

  	$api->get('locations', 'App\Api\V1\Controllers\LocationController@index');
  	$api->post('locations', 'App\Api\V1\Controllers\LocationController@store');
  	$api->delete('locations/{id}', 'App\Api\V1\Controllers\LocationController@destroy');

    $api->get('assets', 'App\Api\V1\Controllers\AssetController@index');
    $api->get('assets/edit', 'App\Api\V1\Controllers\AssetController@indexEdit');
    $api->put('assets/{id}', 'App\Api\V1\Controllers\AssetController@update'); 
    $api->get('assets/{id}/timeranges', 'App\Api\V1\Controllers\AssetController@getTimeRanges');
    $api->post('assets/{id}/timeranges', 'App\Api\V1\Controllers\AssetController@storeTimeRanges');

    $api->post('tasks', 'App\Api\V1\Controllers\TaskController@store');
    $api->get('tasks/created', 'App\Api\V1\Controllers\TaskController@indexCreated');

  });
});
