<?php

namespace App;

use App\Classes\Property;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'details',
    ];

    public function owner() {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function members() {
        return $this->belongsToMany('App\User', 'workspace_members')->withTimestamps();
    }

    public function workspaceMembers() {
        return $this->hasMany('App\WorkspaceMember');
    }

    public function screens() {
        return $this->belongsToMany('App\Screen', 'workspace_screens')->withTimestamps();
    }

    public function memberScreens() {
        return $this->hasManyThrough(
            'App\MemberScreen',
            'App\WorkspaceMember',
            'workspace_id',
            'member_id');
    }

    public function workspaceScreens() {
        return $this->hasMany('App\WorkspaceScreen');
    }

    public function advertisements() {
        return $this->hasMany('App\Advertisement');
    }

    public function screenAdvertisements() {
        return $this->hasManyThrough('App\ScreenAdvertisement', 'App\Advertisement');
    }

    public static function getProperties()
    {
        return [
            new Property('Title', 'hyperlink', function($item){
                return ['link'=>route('workspaces.show', $item->id), 'text'=>$item->title];
            }),
            new Property('Created', null, function($item){ return $item->created_at->diffForhumans(); }),
            new Property('Updated', null, function($item){ return $item->updated_at->diffForhumans(); }),
            new Property('Edit', 'hyperlink', function($item){
                return ['link'=>route('workspaces.edit', $item->id), 'text'=>'Edit'];
            }),
            new Property('Delete', 'delete-button', function($item){
                return ['action'=>'WorkspacesController@destroy', 'id'=>$item->id];
            }),
        ];
    }

    public static function getJoinedProperties()
    {
        return [
            new Property('Title', 'hyperlink', function($item){
                return ['link'=>route('workspaces.show', $item->id), 'text'=>$item->title];
            }),
            new Property('Leave', 'delete-button', function($item){
                return ['action'=>'WorkspacesController@leave', 'id'=>$item->id];
            }),
        ];
    }
}
