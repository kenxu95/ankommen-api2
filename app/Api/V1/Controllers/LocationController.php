<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\Location;
use Dingo\Api\Routing\Helpers;

use App\Http\Controllers\Controller;



class LocationController extends Controller
{
    use Helpers;

    // Responds with all the locations a user has
    public function index()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        return $currentUser
            ->locations()
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
    }

    // Save location
    public function store(Request $request)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();

        // Create new location
        $location = new Location;
        $location->fill($request->all()['location']);

        // Save location
        if ($currentUser->locations()->save($location))
            return $this->response->noContent();
        else
            return $this->response->error('could_not_create_location', 500);
    }

    // Remove location from the database
    public function destroy($id)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();

        // Find the location
        $location = $currentUser->locations()->find($id);
        if (!$location)
            throw new NotFoundHttpException;

        // Delete the location
        if ($location->delete())
            return $this->response->noContent();
        else
            return $this->response->error('could_not_create_location', 500);
    }
}














