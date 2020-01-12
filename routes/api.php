<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::prefix('advertisements')->group(function() {
        Route::post('request', 'ScreenAdvertisementsController@getScreenAdvertisements')
            ->name('screens.advertisements.request');
    });

    Route::post('requestId', function() { return Auth::user()->id; });

//    Route::prefix('advertisements')->group(function () {
//        Route::post('/request', 'AdvertisementsController@getDeviceAdvertisements')
//            ->name('advertisements.getDeviceAdvertisements');
//    });
});

Route::post('test', function(Request $r) { return 'a'.json_encode($r->header()); });
