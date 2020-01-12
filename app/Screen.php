<?php

namespace App;

use App\Classes\Property;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Screen extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'location', 'details',
    ];

    public function owner() {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function workspaces() {
        return $this->belongsToMany('App\Workspace', 'workspace_screens');
    }

    public function advertisements() {
        return $this->belongsToMany('App\Advertisement', 'screen_advertisements');
    }

    public function members() {
        return $this->belongsToMany(
            'App\WorkspaceMember',
            'member_screens',
            'screen_id',
            'member_id');
    }

    public static function getProperties()
    {
        return [
            new Property('ID', 'hyperlink', function($item){
                return ['link'=>route('screens.show', $item->id), 'text'=>$item->id];
            }),
            new Property('Type', null, function($item){ return $item->type; }),
            new Property('Location', null, function($item){ return $item->location; }),
            new Property('Created', null, function($item){ return $item->created_at->diffForhumans(); }),
            new Property('Updated', null, function($item){ return $item->updated_at->diffForhumans(); }),
            new Property('Edit', 'hyperlink', function($item){
                return ['link'=>route('screens.edit', $item->id), 'text'=>'Edit'];
            }),
            new Property('Delete', 'delete-button', function($item){
                return ['action'=>'ScreensController@destroy', 'id'=>$item->id];
            }),
        ];
    }
}
