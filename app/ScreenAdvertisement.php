<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScreenAdvertisement extends Model
{
    public $fillable = ['screen_id'];

    public $timestamps = false;

    public function screen() {
        return $this->belongsTo('App\Screen');
    }

    public function advertisement() {
        return $this->belongsTo('App\Advertisement');
    }
}
