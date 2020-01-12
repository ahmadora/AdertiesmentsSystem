<?php

namespace App\Http\Controllers;

use App\Advertisement;
use App\Events\AdvertisementRemoved;
use App\Privilege;
use App\Screen;
use App\Workspace;
use App\WorkspaceMember;
use App\WorkspaceScreen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class WorkspaceScreensController extends Controller
{
    public function index($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = WorkspaceMember::where('workspace_id', $workspaceId)->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $privileges = $userMember->privileges->map(function($privilege) { return $privilege->name; })->toArray();
        $allowedPrivileges = [
            Privilege::ADD_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS_SCREENS,
            Privilege::REMOVE_SCREENS,
        ];
        $canViewAllScreens = !empty(array_intersect($privileges, $allowedPrivileges));
        if (!$canViewAllScreens) {
            return abort(403, 'Unauthorized');
        }
        $screens = $workspace->workspaceScreens()->paginate();
        $is_owner = $user->id == $workspace->owner_id;
        return view('workspaces/screens/index', compact('screens', 'privileges', 'workspaceId', 'is_owner'));
    }

    public function show($workspaceScreenId)
    {
        $workspaceScreen = WorkspaceScreen::findOrFail($workspaceScreenId);
        $user = Auth::user();
        $userMember = WorkspaceMember::where('workspace_id', $workspaceScreen->workspace_id)
            ->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $privileges = $userMember->privileges->map(function($privilege) { return $privilege->name; })->toArray();
        $allowedPrivileges = [
            Privilege::ADD_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS_SCREENS,
            Privilege::REMOVE_SCREENS,
        ];
        $canViewScreen = !empty(array_intersect($privileges, $allowedPrivileges));
        if (!$canViewScreen) {
            return abort(403, 'Unauthorized');
        }
        $screen = $workspaceScreen->screen;
        $screen_publishers = $screen->members()->where('workspace_id', $workspaceScreen->workspace_id)->get();
        $screen_publishers_ids = $screen_publishers->map(function($screen_publisher) {
            return $screen_publisher->id;
        });
        $other_publishers = $workspaceScreen->workspace->workspaceMembers()->whereNotIn('workspace_members.id', $screen_publishers_ids)->get();
        return view('workspaces/screens/show', compact(
            'workspaceScreen',
            'privileges',
            'screen_publishers',
            'other_publishers'));
    }

    public function add(Request $request, $workspaceId)
    {
        $user = Auth::user();
        $workspace = $user->workspaces()->findOrFail($workspaceId);
        $screenId = $request['id'];
        $request->validate([
            'id' => [
                'required',
                Rule::exists('screens')->where(function($query) use($screenId, $user) {
                    $query->where('id', $screenId)->where('owner_id', $user->id);
                }),
            ],
        ]);
        $inWorkspace = (bool) $workspace->screens()->find($screenId);
        if ($inWorkspace) {
            return redirect()->back()
                ->withErrors(['id' => 'Screen is already in the workspace'])
                ->withInput($request->all());
        }
        $screen = Screen::find($screenId);
        if ($screen->type === 'public') {
            return redirect()->back()
                ->withErrors(['id' => 'Screen is public'])
                ->withInput($request->all());
        }
        $workspace->screens()->attach($screenId);
        Session::flash('info', 'Screen #'.$screenId.' has been added to workspace');
        return redirect()->action('WorkspaceScreensController@index', ['workspaceId' => $workspace->id]);
    }

    public function remove($workspaceScreenId)
    {
        $workspaceScreen = WorkspaceScreen::findOrFail($workspaceScreenId);
        $workspace = $workspaceScreen->workspace;
        $user = Auth::user();
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember || !$userMember->privileges()->where('name', Privilege::REMOVE_SCREENS)->exists()) {
            return abort(403, 'Unauthorized');
        }
        $workspace->memberScreens()->where('screen_id', $workspaceScreen->screen_id)->delete();

        $screen_advertisements_query = $workspace->screenAdvertisements()
            ->where('screen_id', $workspaceScreen->screen_id);
        $screen_advertisements = $screen_advertisements_query->get();
        $screen_advertisements_query->delete();
        foreach($screen_advertisements as $screen_advertisement) {
            event(new AdvertisementRemoved($screen_advertisement->screen_id,
                ['id' => $screen_advertisement->advertisement_id]));
        }
        $workspaceScreen->delete();
        Session::flash('danger', 'Screen #'.$workspaceScreen->screen_id.' has been removed from workspace');
        return redirect()->action('WorkspaceScreensController@index', ['workspaceId' => $workspaceScreen->workspace_id]);
    }

    public function addToMember(Request $request, $workspaceId, $screenId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = $workspace->members()->find($user->id);
        if (!$userMember || $userMember->privileges()->where('name', Privilege::ADD_MEMBERS_SCREENS)->exists()) {
            return abort(403, 'Unauthorized');
        }
        $screen = $workspace->screens()->findOrFail($screenId);
        $memberId = $request['member_id'];
        $request->validate([
            'member_id' => [
                'required',
                Rule::exists('workspace_members')->where(function($query) use($workspaceId, $memberId) {
                    $query->where('member_id', $memberId)->where('workspace_id', $workspaceId);
                }),
            ],
        ]);
        $screen->members()->attach($memberId);
        return redirect()->action('WorkspacesController@show', ['id' => $workspace->id]);
    }

    public function removeFromMember($workspaceId, $screenId, $memberId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = $workspace->members()->find($user->id);
        if (!$userMember || $userMember->privileges()->where('name', Privilege::REMOVE_MEMBERS_SCREENS)->exists()) {
            return abort(403, 'Unauthorized');
        }
        $screen = $workspace->screens()->findOrFail($screenId);
        $screen->members()->detach($memberId);
        return redirect()->action('WorkspacesController@show', ['id' => $workspace->id]);
    }

    public function updatePublishers(Request $request, $workspaceScreenId)
    {
        $workspaceScreen = WorkspaceScreen::findOrFail($workspaceScreenId);
        $user = Auth::user();
        $user_member = WorkspaceMember::where('user_id', $user->id)->where('workspace_id', $workspaceScreen->workspace_id)->first();
        if (!$user_member) {
            return abort(403, 'Unauthorized');
        }
        $screen = $workspaceScreen->screen;

        $user_privileges = $user_member->privileges->map(function($privilege) { return $privilege->name; })->toArray();

        if (in_array(Privilege::ADD_MEMBERS_SCREENS, $user_privileges)) {
            $new_publishers = $request['new_publishers'];
            if ($new_publishers) {
                foreach ($new_publishers as $new_publisher) {
                    $screen->members()->attach($new_publisher);
                }
            }
        }

        if (in_array(Privilege::REMOVE_MEMBERS_SCREENS, $user_privileges)) {
            $remove_publishers = $request['remove_publishers'];
            if ($remove_publishers) {
                foreach ($remove_publishers as $remove_publisher) {
                    $screen->members()->detach($remove_publisher);
                }
            }
        }
        Session::flash('info', 'Screen publishers has been updated successfully.');
        return redirect()->back();
    }

    public function advertisements($workspaceScreenId)
    {
        $workspaceScreen = WorkspaceScreen::findOrFail($workspaceScreenId);
        $user = Auth::user();
        $user_member = WorkspaceMember::where('user_id', $user->id)->where('workspace_id', $workspaceScreen->workspace_id)->first();
        $privileges = $user_member->privileges->map(function($privilege) { return $privilege->name; })->toArray();
        $allowedPrivileges = [
            Privilege::REMOVE_ADVERTISEMENTS,
            Privilege::UPDATE_ADVERTISEMENTS,
        ];
        if (empty(array_intersect($privileges, $allowedPrivileges))) {
            return abort(403, 'Unauthorized');
        }

        $advertisements = $workspaceScreen->screen->advertisements()
            ->where('workspace_id', $workspaceScreen->workspace_id)->paginate();

        return view('workspaces/screens/advertisements', compact('workspaceScreen','advertisements', 'privileges'));
    }
}
