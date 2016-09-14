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

  // Respond with all the assets
  public function index()
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $allAssets = \DB::table('assets')->get();
    return response()
           ->json(array('allAssets' => $allAssets))
           ->header('Cache-Control', 'public');
  }

  // Response will all the assets, split into two disjoint groups
  // 1) User Assets 2) Potential Assets
  public function indexEdit() 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $userAssets = $currentUser
    ->userAssets()
    ->orderBy('name', 'ASC')
    ->get();

    // Create array of Assets
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

  // Update the user asset's for the current user (either add or remove user asset)
  public function update(Request $request, $id) 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    $asset = \App\Asset::find($id); // Get the asset
    if (!$asset)
      throw new NotFoundHttpException;

    // Adding an UserAsset
    if ($request->get('action') == 'add')
    {
      $userAsset = new UserAsset;
      $userAsset->name = $asset->name;

      // Save through User and Asset
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

  // Convert backend time (in minutes) to string
  private function numFromTime($hour, $minutes){
    return ($hour * 2) + ($minutes == 0 ? 0 : 1);
  }

  // Get the time ranges for a particular asset
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
        'timeRange' => array(
          $this->numFromTime($dayTimeRange->startHour, $dayTimeRange->startMinutes),
          $this->numFromTime($dayTimeRange->endHour, $dayTimeRange->endMinutes))
      ));
    }
    return response()->json(array('dayTimeRanges' => $timeRanges));
  }  

  // Save the time ranges
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

    // Save each time range 
    foreach ($request->all() as $dayTimeRange)
    {
      // Create a new tmie range
      $timeRange = new TimeRange;
      $timeRange->weekday = $dayTimeRange['day'];
      $timeRange->startHour = floor($dayTimeRange['timeRange'][0] / 2);
      $timeRange->startMinutes = $dayTimeRange['timeRange'][0] % 2 == 0 ? 0 : 30;
      $timeRange->endHour = floor($dayTimeRange['timeRange'][1] / 2);
      $timeRange->endMinutes = $dayTimeRange['timeRange'][1] % 2 == 0 ? 0 : 30;

      // Save time range
      if (! $userAsset->timeRanges()->save($timeRange)){
        return $this->response->error('could_not_save_time_range', 500);
      }
    }
    return $this->response->nocontent();
  }

}












