<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeRange extends Model
{
    protected $fillable = [
      'weekday', 'startHour', 'startMinutes', 'endHour', 'endMinutes'
    ];

    public function userAsset(){
      return $this->belongsTo('App\UserAsset');
    }
}
