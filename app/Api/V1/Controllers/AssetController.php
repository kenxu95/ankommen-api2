<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\Asset;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

class AssetController extends Controller
{
  use Helpers;


  public function index() {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $userAssets = $currentUser
                    ->userAssets()
                    ->orderBy('name', 'ASC')
                    ->get()
                    ->toArray();

    $assets = array_map(function($o) { return $o->asset; }, $userAssets);

    $potentialAssets = \DB::table('assets')
                        ->whereNotIn('id', 
                          array_map(function($o) { return $o->id; }, $userAssets))
                        ->orderBy('name', 'ASC')
                        ->get();

    return response()
           ->json(array('user' => $assets, 'potential' => $potentialAssets))
           ->header('Cache-Control', 'public');
  } 
}
