<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taskasset extends Model
{
    protected $fillable = [
      'needed'
    ];

    public function task(){
      return $this->belongsTo('App\Task');
    }

    public function asset(){
      return $this->belongsTo('App\Asset');
    }
}
