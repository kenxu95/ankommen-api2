<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**x
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * This mutator automatically hashes the password.
     *
     * @var string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }


    public function locations() {
        return $this->hasMany('App\Location');
    }

    public function image() {
        return $this->hasOne('App\Image');
    }

    public function userAssets() {
        return $this->hasMany('App\UserAsset');
    }

    public function createdTasks() {
        return $this->hasMany('App\Task');
    }
}
