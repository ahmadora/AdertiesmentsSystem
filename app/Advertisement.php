<?php

namespace App;

use App\Classes\Property;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'url', 'priority', 'expires_at', 'workspace_id',
    ];

    public function creator() {
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function screens() {
        return $this->belongsToMany('App\Screen', 'screen_advertisements');
    }

    public function workspace() {
        return $this->belongsTo('App\Advertisement');
    }

    public static function getProperties($privileges)
    {
        $res = [
            new Property('Title', null, function($item){ return $item->title; }),
            new Property('Created', null, function($item){ return $item->created_at->diffForhumans(); }),
            new Property('Updated', null, function($item){ return $item->updated_at->diffForhumans(); }),
        ];
        if (!$privileges || in_array(Privilege::UPDATE_ADVERTISEMENTS, $privileges)) {
            array_push($res, new Property('Edit', 'hyperlink', function($item){
                return ['link'=>route('advertisements.edit', $item->id), 'text'=>'Edit'];
            }));
        }
        if (!$privileges || in_array(Privilege::REMOVE_ADVERTISEMENTS, $privileges)) {
            array_push($res, new Property('Delete', 'delete-button', function($item) {
                return ['action'=>'AdvertisementsController@destroy', 'id'=>$item->id];
            }));
        }
        return $res;
    }
}
