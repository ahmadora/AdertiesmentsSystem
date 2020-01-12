<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class Privilege extends Model
{
    const ADD_MEMBERS_SCREENS = 'add-members-screens';
    const REMOVE_MEMBERS_SCREENS = 'remove-members-screens';
    const REMOVE_SCREENS = 'remove-screens';
    const ADD_MEMBERS = 'add-members';
    const REMOVE_MEMBERS = 'remove-members';
    const UPDATE_ADVERTISEMENTS = 'update-advertisements';
    const REMOVE_ADVERTISEMENTS = 'remove-advertisements';
    //const UPDATE_PRIVILEGES = 'update-privileges';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public function members() {
        return $this->belongsToMany(
            'App\WorkspaceMember',
            'member_privileges',
            'privilege_id',
            'member_id');
    }

    static function getAllPrivileges() {
        return [
            Privilege::ADD_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS_SCREENS,
            Privilege::REMOVE_SCREENS,
            Privilege::ADD_MEMBERS,
            Privilege::REMOVE_MEMBERS,
            Privilege::UPDATE_ADVERTISEMENTS,
            Privilege::REMOVE_ADVERTISEMENTS,
            //Privilege::UPDATE_PRIVILEGES,
        ];
    }

    static function getAllPrivilegesDetailed() {
        return [
            [
                'name' => Privilege::ADD_MEMBERS_SCREENS,
                'display_name' => 'Add screens to members',
                'details' => 'Add screens to members so they can publish advertisements on.',
            ],
            [
                'name' => Privilege::REMOVE_MEMBERS_SCREENS,
                'display_name' => 'Remove screens from members',
                'details' => 'Remove screens from members so they can\'t publish advertisements on.',
            ],
            [
                'name' => Privilege::REMOVE_SCREENS,
                'display_name' => 'Remove screens',
                'details' => 'Remove screens from the workspace.',
            ],
            [
                'name' => Privilege::ADD_MEMBERS,
                'display_name' => 'Add members',
                'details' => 'Add members to the workspace.',
            ],
            [
                'name' => Privilege::REMOVE_MEMBERS,
                'display_name' => 'Remove members',
                'details' => 'Remove members from the workspace.',
            ],
            [
                'name' => Privilege::UPDATE_ADVERTISEMENTS,
                'display_name' => 'Update advertisements',
                'details' => 'Update advertisements in the workspace.',
            ],
            [
                'name' => Privilege::REMOVE_ADVERTISEMENTS,
                'display_name' => 'Remove advertisements',
                'details' => 'Remove advertisements from the workspace.',
            ],
            //Privilege::UPDATE_PRIVILEGES,
        ];
    }
}
