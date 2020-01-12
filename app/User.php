<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'birth_date',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role() {
        return $this->belongsTo('App\Role');
    }

    public function screens() {
        return $this->hasMany('App\Screen', 'owner_id');
    }

    public function workspaces() {
        return $this->hasMany('App\Workspace', 'owner_id');
    }

    public function joined_workspaces() {
        return $this->belongsToMany('App\Workspace', 'workspace_members');
    }

    public function advertisements() {
        return $this->hasMany('App\Advertisement', 'creator_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    
    public function getNameAttribute() {
        return ucfirst($this->attributes['first_name']) . ' ' . ucfirst($this->attributes['last_name']);
    }

}
