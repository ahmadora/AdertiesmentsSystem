<?php

namespace App;

use App\Classes\Property;
use Illuminate\Database\Eloquent\Model;

class WorkspaceScreen extends Model
{
    public function workspace() {
        return $this->belongsTo('App\Workspace');
    }

    public function screen() {
        return $this->belongsTo('App\Screen');
    }

    public function members() {
        $screen = Screen::findOrFail($this->screen_id);
        return $screen->members()->where('workspace_id', $this->workspace_id);
    }

    public static function getProperties($privileges)
    {
        $res = [
            new Property('ID', null, function($item){ return $item->screen->id; }),
            new Property('Location', null, function($item){ return $item->screen->location; }),
            new Property('Publishers', null, function($item){ return $item->members()->count().' publishers'; }),
        ];
        if (in_array(Privilege::ADD_MEMBERS_SCREENS, $privileges) ||
            in_array(Privilege::REMOVE_MEMBERS_SCREENS, $privileges)) {
            array_push($res, new Property('Control', 'hyperlink', function($item){
                return ['link'=>route('workspaces.screens.show', $item->id), 'text'=>'Control'];
            }));
        }
        if (in_array(Privilege::REMOVE_SCREENS, $privileges)) {
            array_push($res, new Property('Delete', 'delete-button', function($item) {
                return ['action'=>'WorkspaceScreensController@remove', 'id'=>$item->id];
            }));
        }
        return $res;
    }
}
