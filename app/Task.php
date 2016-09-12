<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
      'name', 'description', 'date', 'time', 'duration'
    ];

    public function taskassets(){
      return $this->hasMany('App\Taskasset');
    }

    public function locations(){
      return $this->hasMany('App\Location');
    }

    public function userCreator(){
      return $this->belongsTo('App\User');
    }
}
