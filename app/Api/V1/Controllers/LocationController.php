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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        return $currentUser
            ->locations()
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $location = new Location;
        $location->fill($request->all()['location']);

        if ($currentUser->locations()->save($location))
            return $this->response->noContent();
        else
            return $this->response->error('could_not_create_location', 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        $location = $currentUser->locations()->find($id);
        if (!$location)
            throw new NotFoundHttpException;

        if ($location->delete())
            return $this->response->noContent();
        else
            return $this->response->error('could_not_create_location', 500);
    }
}














