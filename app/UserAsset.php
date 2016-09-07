<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAsset extends Model
{
  protected $fillable = [
    'name'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function asset() {
    return $this->belongsTo('App\Asset');
  }

  public function timeranges() {
    return $this->hasMany('App\TimeRange');
  } 
}

