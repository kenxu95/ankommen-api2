<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\Asset;
use App\UserAsset;
use App\TimeRange;
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

  public function update(Request $request, $id) 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    $asset = \App\Asset::find($id); // Get the asset
    if (!$asset)
      throw new NotFoundHttpException;

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


  public function storeTimeRanges(Request $request, $id)
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    $asset = \App\Asset::find($id); // Get the asset
    if (!$asset)
      throw new NotFoundHttpException;
    $userAsset = $currentUser->userAssets()->where('name', $asset->name)->first();

    Log::info($userAsset->timeRanges);
    return $this->response->nocontent();

    // Remove all previous time ranges
    if (count($userAsset->timeRanges) > 0)
    {
      if (! $userAsset->timeRanges()->delete())
      {
        return $this->response->error('could_not_save_availability', 500);
      }
    }

    // Add in all new time ranges
    foreach ($request->all() as $dayData)
    {
      foreach($dayData['timeranges'] as $dayTimeRange)
      {
        $timeRange = new TimeRange;
        $timeRange->weekday = $dayData['day'];
        $timeRange->startHour = floor($dayTimeRange[0] / 2);
        $timeRange->startMinutes = $dayTimeRange[0] % 2 == 0 ? 0 : 30;
        $timeRange->endHour = floor($dayTimeRange[1] / 2);
        $timeRange->endMinutes = $dayTimeRange[1] % 2 == 0 ? 0 : 30;

        if (! $userAsset->timeRanges()->save($timeRange)){
          return $this->response->error('could_not_save_time_range', 500);
        }
      }
    }

    return $this->response->nocontent();
  }

}












