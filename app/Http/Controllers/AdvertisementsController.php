<?php

namespace App\Http\Controllers;

use App\Advertisement;
use App\Events\AdvertisementAdded;
use App\Events\AdvertisementRemoved;
use App\Helpers\FilesHelper;
use App\Privilege;
use App\Workspace;
use App\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdvertisementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $advertisements = $user->advertisements()->paginate();
        return view('advertisements/index', compact('advertisements'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $advertisement = Advertisement::find($id);
        if (!$advertisement) {
            Session::flash('danger', 'Advertisement not found');
        }
        else {
            $user = Auth::user();
            if ($user->id != $advertisement->creator_id) {
                $userMember = WorkspaceMember::where('workspace_id', $advertisement->workspace_id)
                    ->where('user_id', $user->id)->first();
                if (!$userMember ||
                    !$userMember->privileges()->where('name', Privilege::UPDATE_ADVERTISEMENTS)->exists()) {
                    Session::flash('danger', "You don't have permission to edit this advertisement");
                    return redirect()->back();
                }
            }
            $advertisement_screens = $advertisement->screens()->get();
            $other_screens = null;
            if ($advertisement->workspace_id) {
                $advertisement_screens_ids = $advertisement_screens->map(function($ad_screen) {
                    return $ad_screen->id;
                });
                $other_screens = Workspace::findOrFail($advertisement->workspace_id)
                    ->screens()->whereNotIn('screens.id', $advertisement_screens_ids)->get();

            }
            return view('advertisements/edit',
                compact('advertisement', 'other_screens', 'advertisement_screens'));
        }
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $user = Auth::user();
        if ($user->id != $advertisement->creator_id) {
            Session::flash('danger', "You don't have permission to edit this advertisement");
        } else {
            // Add new screens
            $new_screens = $request['new_screens'];
            if ($new_screens) {
                foreach ($new_screens as $new_screen) {
                    $advertisement->screens()->attach($new_screen);
                    event(new AdvertisementAdded($new_screen, [
                        'id' => $advertisement->id,
                        'url' => FilesHelper::getFileURL(FilesHelper::ADVERTISEMENT, $advertisement->url),
                        'priority' => $advertisement->priority,
                        'expires_at' => $advertisement->expires_at,
                    ]));
                }
            }
            // Remove existing screens
            $remove_screens = $request['remove_screens'];
            if ($remove_screens) {
                foreach ($remove_screens as $remove_screen) {
                    $advertisement->screens()->detach($remove_screen);
                    event(new AdvertisementRemoved($remove_screen, ['id' => $advertisement->id]));
                }
            }
            // Update advertisement {updated at column}
            $advertisement->touch();
            Session::flash('info', ['action' => 'updated']);
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $user = Auth::user();
        if ($user->id != $advertisement->creator_id) {
            $userMember = WorkspaceMember::where('workspace_id', $advertisement->workspace_id)
                ->where('user_id', $user->id)->first();
            if (!$userMember ||
                !$userMember->privileges()->where('name', Privilege::REMOVE_ADVERTISEMENTS)->exists()) {
                Session::flash('danger', "You don't have permission to remove this advertisement");
                return redirect()->back();
            }
        }
        $screens = $advertisement->screens()->get();
        foreach ($screens as $screen) {
            event(new AdvertisementRemoved($screen->id, $advertisement));
        }
        $advertisement->delete();
        FilesHelper::removeFile(FilesHelper::ADVERTISEMENT, $advertisement->url);
        Session::flash('danger', 'Advertisement has been removed successfully');
        return redirect()->back();
    }
}
