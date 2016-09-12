<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
      'name', 'description', 'dataurl'
    ];

    public function userAssets() {
      return $this->hasMany('App\UserAsset');
    }

    public function taskassets() {
      return $this->hasMany('App\Taskasset');
    }
}
