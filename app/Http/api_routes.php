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

    $api->get('user/image', 'App\Api\V1\Controllers\UserController@imageShow');
    $api->post('user/image', 'App\Api\V1\Controllers\UserController@imageStore');

  	$api->get('locations', 'App\Api\V1\Controllers\LocationController@index');
  	$api->post('locations', 'App\Api\V1\Controllers\LocationController@store');
  	$api->delete('locations/{id}', 'App\Api\V1\Controllers\LocationController@destroy');
  });
});
