<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\User;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
  use Helpers;

  public function index() 
  {
    //$currentUser = JWTAuth::parseToken()->authenticate();

    $users = User::all();


    return response()->json(array(
        'error' => false,
        'users' => $users->toArray()),
        200
      )->header('Access-Control-Allow-Origin', '*')
       ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
       ->header('Access-Control-Allow-Headers', 'Authorization,X-CSRF-Token,x-csrf-token');
  }
}
