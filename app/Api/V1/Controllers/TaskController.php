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

  // Return the associated array of assets from taskassets
  private function getAssets(Task $task)
  {
    $assets = [];
    foreach($task->taskassets as $taskasset)
    {
      array_push($assets, $taskasset->asset);
    }
    return $assets;
  }

  // Return -1 if the task has already finished
  // Return 0 if the task is currently happening
  // Return 1 if the task will take place in the future
  private function compareTime($task)
  {
    // $taskDate = DateTime::createFromFormat('d/m/Y', $task->date); 
    // $currentDate = new DateTime();
    return 1;

  }

  // Respond with all the tasks that the user created himself
  // TODO: Deal with tasks that are currently executing
  public function indexCreated()
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $createdTasks = [];
    foreach($currentUser->createdTasks as $createdTask){
      if ($this->compareTime($createdTask) > 0){
        array_push($createdTasks, 
          array('task' => $createdTask,
           'assets' => $this->getAssets($createdTask),
           'locations' => $createdTask->locations));
      }
    }
    return response()->json($createdTasks);
  }

  // Respond with all the user-created tasks, which have already occured
  public function indexPrevious()
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    
    $createdTasks = [];
    foreach($currentUser->createdTasks as $createdTask){
      if ($this->compareTime($createdTask) < 0){
        array_push($createdTasks, 
          array('task' => $createdTask,
            'assets' => $this->getAssets($createdTask),
            'locations' => $createdTask->locations));
      }
    }
    return response()->json($createdTasks);
  }

  // Store the task
  public function storeCreated(Request $request)
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














