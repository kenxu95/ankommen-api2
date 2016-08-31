<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\User;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
  use Helpers;

  public function show() 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    return $currentUser;
  }

  public function update(Request $request) 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $currentUser->fill(($request->all()['user']));

    if ($currentUser->save())
      return $this->response->noContent();
    else
      return $this->response->error('could_not_update_user', 500);
  } 
}
