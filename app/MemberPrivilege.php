<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberPrivilege extends Model
{
    public $timestamps = false;

    public function member() {
        return $this->belongsTo('App\WorkspaceMember', 'member_id');
    }

    public function privilege() {
        return $this->belongsTo('App\Privilege');
    }
}
