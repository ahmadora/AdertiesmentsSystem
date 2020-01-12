<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberScreen extends Model
{
    public $timestamps = false;

    public function member() {
        return $this->belongsTo('App\WorkspaceMember', 'member_id');
    }

    public function screen() {
        return $this->belongsTo('App\Screen');
    }
}
