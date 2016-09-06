<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
      'filename'
    ];

    public function user() {
      return $this->belongsTo('App\User');
    }
}

