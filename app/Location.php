<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name', 'longitude', 'latitude', 'radius'
    ];

    public function user() {
      return $this->belongsTo('App\User');
    }
}
