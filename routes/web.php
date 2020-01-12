<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function () {
    Route::prefix('dashboard')->group(function() {
        Route::resource('workspaces', 'WorkspacesController');
        Route::prefix('workspaces')->group(function() {
            Route::prefix('screens')->group(function() {
                Route::get('{workspaceId}', 'WorkspaceScreensController@index')
                    ->name('workspaces.screens.index');
                Route::get('show/{workspaceScreenId}', 'WorkspaceScreensController@show')
                    ->name('workspaces.screens.show');
                Route::get('advertisements/{workspaceScreenId}', 'WorkspaceScreensController@advertisements')
                    ->name('workspaces.screens.advertisements');
                Route::post('{workspaceId}/add', 'WorkspaceScreensController@add')
                    ->name('workspaces.screens.add');
                Route::delete('remove/{workspaceScreenId}', 'WorkspaceScreensController@remove')
                    ->name('workspaces.screens.remove');
                Route::post('addToMember/{workspaceId}/{screenId}', 'WorkspaceScreensController@addToMember')
                    ->name('workspaces.screens.addToMember');
                Route::delete('removeFromMember/{workspaceId}/{screenId}/{memberId}',
                    'WorkspaceScreensController@removeFromMember')
                    ->name('workspaces.screens.removeFromMember');
                Route::patch('updatePublishers/{workspaceScreenId}', 'WorkspaceScreensController@updatePublishers')
                    ->name('workspaces.members.updatePublishers');
            });
            Route::prefix('members')->group(function() {
                Route::get('{workspaceId}', 'WorkspaceMembersController@index')
                    ->name('workspaces.members.index');
                Route::get('show/{memberId}', 'WorkspaceMembersController@show')
                    ->name('workspaces.members.show');
                Route::get('advertisements/{memberId}', 'WorkspaceMembersController@advertisements')
                    ->name('workspaces.members.advertisements');
                Route::post('{workspaceId}/add', 'WorkspaceMembersController@add')
                    ->name('workspaces.members.add');
                Route::delete('remove/{memberId}', 'WorkspaceMembersController@remove')
                    ->name('workspaces.members.remove');
                Route::patch('updatePrivileges/{memberId}', 'WorkspaceMembersController@updatePrivileges')
                    ->name('workspaces.members.updatePrivileges');
            });
            Route::prefix('advertisements/{workspaceId}')->group(function() {
               Route::get('index', 'WorkspaceAdvertisementsController@index')
                   ->name('workspaces.advertisements.index');
                Route::get('own', 'WorkspaceAdvertisementsController@ownIndex')
                    ->name('workspaces.advertisements.own');
                Route::get('create', 'WorkspaceAdvertisementsController@create')
                    ->name('workspaces.advertisements.create');
                Route::post('store', 'WorkspaceAdvertisementsController@store')
                    ->name('workspaces.advertisements.store');
            });
        });
        Route::resource('screens', 'ScreensController');
        Route::prefix('screens')->group(function() {
            Route::get('advertisements/{screenId}', 'ScreenAdvertisementsController@index')
                ->name('screens.advertisements.index');
            Route::get('downloadConfig/{screenId}', 'ScreensController@downloadConfigFile')
                ->name('screens.downloadConfig');
        });
        Route::prefix('advertisements')->group(function() {
            Route::get('index', 'AdvertisementsController@index')
                ->name('advertisements.index');
            Route::get('edit/{id}', 'AdvertisementsController@edit')
                ->name('advertisements.edit');
            Route::put('{id}', 'AdvertisementsController@update')
                ->name('advertisements.update');
            Route::delete('{id}', 'AdvertisementsController@destroy')
                ->name('advertisements.delete');
        });
    });
});
