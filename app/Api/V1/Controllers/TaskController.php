<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\Task;
use App\Taskasset;
use App\Location;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
  use Helpers;

  public function indexCreated()
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $createdTasks = [];
    foreach($currentUser->createdTasks as $createdTask){
      array_push($createdTasks, 
        array('task' => $createdTask,
              'taskassets' => $createdTask->taskassets,
              'locations' => $createdTask->locations));
    }
    return response()->json($createdTasks);
  }



  public function store(Request $request)
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    // Create and Save Task 
    $task = new Task;
    $task->fill($request->get('task'));
    if (! $currentUser->createdTasks()->save($task))
      return $this->response->error('could_not_save_task');

    // Create and link a Taskasset for each asset on the task
    $reqAssets = $request->get('assets');
    $reqNeeded = $request->get('needed');
    for ($i = 0; $i < count($reqAssets); $i++)
    {
      $asset = \App\Asset::find($reqAssets[$i]['id']); // Get the asset

      // Create the Taskasset
      $taskasset = new Taskasset;
      $taskasset->needed = $reqNeeded[$i];

      // Save the Taskasset
      if (! $asset->taskassets()->save($taskasset) ||
          ! $task->taskassets()->save($taskasset))
        return $this->response->error('could_not_save_asset_reference');
    }

    // Create and Save the Locations
    foreach ($request->input('locations') as $reqLocation)
    {
      $location = new Location;
      $location->fill($reqLocation);
      if (! $task->locations()->save($location))
        return $this->response->error('could_not_save_location_reference');
    }

    return $this->response()->noContent();
  }    
}














