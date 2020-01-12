<?php

namespace App;

use App\Classes\Property;
use Illuminate\Database\Eloquent\Model;

class WorkspaceMember extends Model
{
    public function workspace() {
        return $this->belongsTo('App\Workspace');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function screens() {
        return $this->belongsToMany('App\Screen', 'member_screens', 'member_id');
    }

    public function privileges() {
        return $this->belongsToMany('App\Privilege', 'member_privileges', 'member_id');
    }

    public function addPrivileges($privilegeNames) {
        foreach ($privilegeNames as $privilegeName) {
            $privilege = Privilege::where('name', $privilegeName)->first();
            if ($privilege) {
                $privilege->members()->attach($this->id);
            }
        }
    }

    public static function getProperties($privileges)
    {
        $res = [
            new Property('Name', null, function($item){
                $user = $item->user;
                return $user->first_name . ' '. $user->last_name;
            }),
            new Property('Joined in', null, function($item){ return $item->created_at->diffForhumans(); }),
        ];
        if (in_array(Privilege::ADD_MEMBERS_SCREENS, $privileges) ||
            in_array(Privilege::REMOVE_MEMBERS_SCREENS, $privileges)) {
            array_push($res, new Property('Control', 'hyperlink', function($item){
                return ['link'=>route('workspaces.members.show', $item->id), 'text'=>'Control'];
            }));
        }
        if (in_array(Privilege::REMOVE_MEMBERS, $privileges)) {
            array_push($res, new Property('Delete', 'delete-button', function($item) {
                return ['action'=>'WorkspaceMembersController@remove', 'id'=>$item->id];
            }));
        }
        return $res;
    }
}
