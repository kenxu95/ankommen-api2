<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\Asset;
use App\UserAsset;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;


class AssetController extends Controller
{
  use Helpers;

  public function index() 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $userAssets = $currentUser
    ->userAssets()
    ->orderBy('name', 'ASC')
    ->get();

    // Build an array of User Assets
    $assets = [];
    foreach ($userAssets as $userAsset)
    {
      array_push($assets, $userAsset->asset);
    }

    // Build an array of Potential Assets
    $potentialAssets = \DB::table('assets')
    ->whereNotIn('id', 
      array_map(function($o) { return $o['id']; }, $assets))
    ->orderBy('name', 'ASC')
    ->get();

    return response()
    ->json(array('user' => $assets, 'potential' => $potentialAssets))
    ->header('Cache-Control', 'public');
  } 

  public function update(Request $request, $id) {
    $currentUser = JWTAuth::parseToken()->authenticate();

    // Retrieve the asset
    $asset = \App\Asset::find($id);

    if (!$asset)
      throw new NotFoundHttpException;


    // Log::info(\DB::table('user_assets')->get()); 
    // Log::info($currentUser->userAssets);
    // return $this->response->nocontent();
      //Log::info($asset->userAssets);


    // Check if this user already has this asset
    if ($request->get('action') == 'add')
    {
      $userAsset = new UserAsset;
      $userAsset->name = $asset->name;

      // Attach to User and Asset
      if ($currentUser->userAssets()->save($userAsset) && 
        $asset->userAssets()->save($userAsset)) // TODO: double save?
      { 
        return $this->response->nocontent();
      } 
    }else 
    {
      // Remove the User Asset
      $userAsset = $currentUser->userAssets()->where('name', $asset->name)->first();

      if ($userAsset->delete()) 
      {
        return $this->response->noContent();
      }
    }
    return $this->response->error('could_not_update_user_asset', 500);
  }


}












