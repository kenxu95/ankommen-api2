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



class AssetController extends Controller
{
  use Helpers;

  // Get all the assets
  public function index()
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $allAssets = \DB::table('assets')->get();
    return response()
           ->json(array('allAssets' => $allAssets))
           ->header('Cache-Control', 'public');
  }

  public function indexEdit() 
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
        $asset->userAssets()->save($userAsset))
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

  // Convert backend time to frontend time
  private function numFromTime($hour, $minutes){
    return ($hour * 2) + ($minutes == 0 ? 0 : 1);
  }

  public function getTimeRanges($id){
    $currentUser = JWTAuth::parseToken()->authenticate();
    $asset = \App\Asset::find($id); // Get the asset
    if (!$asset)
      throw new NotFoundHttpException;
    $userAsset = $currentUser->userAssets()->where('name', $asset->name)->first();

    // Get all time ranges associated with User Asset
    $timeRanges = [];
    foreach ($userAsset->timeRanges as $dayTimeRange){
      array_push($timeRanges, array(
        'day' => $dayTimeRange->weekday,
        'timeRange' => array($this->numFromTime($dayTimeRange->startHour, $dayTimeRange->startMinutes),
                             $this->numFromTime($dayTimeRange->endHour, $dayTimeRange->endMinutes))
      ));
    }
    return response()->json(array('dayTimeRanges' => $timeRanges));
  }  

  public function storeTimeRanges(Request $request, $id)
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    $asset = \App\Asset::find($id); // Get the asset
    if (!$asset)
      throw new NotFoundHttpException;
    $userAsset = $currentUser->userAssets()->where('name', $asset->name)->first();

    // Remove all previous time ranges
    if (count($userAsset->timeRanges) > 0)
    {
      if (! $userAsset->timeRanges()->delete())
      {
        return $this->response->error('could_not_save_availability', 500);
      }
    }

    // Add in all new time ranges
    foreach ($request->all() as $dayTimeRange)
    {
      $timeRange = new TimeRange;
      $timeRange->weekday = $dayTimeRange['day'];
      $timeRange->startHour = floor($dayTimeRange['timeRange'][0] / 2);
      $timeRange->startMinutes = $dayTimeRange['timeRange'][0] % 2 == 0 ? 0 : 30;
      $timeRange->endHour = floor($dayTimeRange['timeRange'][1] / 2);
      $timeRange->endMinutes = $dayTimeRange['timeRange'][1] % 2 == 0 ? 0 : 30;

      if (! $userAsset->timeRanges()->save($timeRange)){
        return $this->response->error('could_not_save_time_range', 500);
      }
    }
    return $this->response->nocontent();
  }

}












