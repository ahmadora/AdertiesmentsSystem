<?php

namespace App\Http\Controllers;

use App\Advertisement;
use App\Privilege;
use App\User;
use App\Workspace;
use App\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WorkspaceMembersController extends Controller
{
    public function index($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $privileges = $userMember->privileges->map(function($privilege) { return $privilege->name; })->toArray();
        $allowedPrivileges = [
            Privilege::ADD_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS,
        ];
        if (empty(array_intersect($privileges, $allowedPrivileges))) {
            return abort(403, 'Unauthorized');
        }
        $members = $workspace->workspaceMembers()->where('user_id', '!=', $user->id)->paginate();
        $workspaceId = $workspace->id;
        return view('workspaces/members/index', compact('members', 'privileges', 'workspaceId'));
    }

    public function show($memberId)
    {
        $member = WorkspaceMember::findOrFail($memberId);
        $user = Auth::user();
        if ($member->user_id == $user->id) {
            return abort(403, 'Unauthorized');
        }
        $workspace = $member->workspace;
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember || $member->user_id == $workspace->owner_id) {
            return abort(403, 'Unauthorized');
        }
        $privileges = $userMember->privileges->map(function($privilege) { return $privilege->name; })->toArray();
        $allowedPrivileges = [
            Privilege::ADD_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS_SCREENS,
            Privilege::REMOVE_MEMBERS,
        ];
        if (empty(array_intersect($privileges, $allowedPrivileges))) {
            return abort(403, 'Unauthorized');
        }
        $member_screens = $member->screens()->get();
        $member_screens_ids = $member_screens->map(function($member_screen) {
            return $member_screen->id;
        });
        $other_screens = $workspace->screens()->whereNotIn('screens.id', $member_screens_ids)->get();
        $member_privileges = $member->privileges()->get();
        $member_privileges_ids = $member_privileges->map(function($member_privilege) {
            return $member_privilege->id;
        });
        $other_privileges = Privilege::whereNotIn('id', $member_privileges_ids)->get();
        $is_owner = $workspace->owner_id == $user->id;
        return view('workspaces/members/show',
            compact(
                'member',
                'privileges',
                'member_screens',
                'other_screens',
                'member_privileges',
                'other_privileges',
                'is_owner'));
    }

    public function add(Request $request, $workspaceId)
    {
        $user = Auth::user();
        $workspace = Workspace::findOrFail($workspaceId);
        $userEmail = $request['email'];
        $request->validate([
            'email' => 'required|not_in:'.$user->email.'|exists:users,email',
        ]);
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $canAddMembers = $userMember->privileges()->where('name', Privilege::ADD_MEMBERS)->exists();
        if (!$canAddMembers) {
            return abort(403, 'Unauthorized');
        }
        $userToAdd = User::where('email', $userEmail)->first();
        $inWorkspace = (bool) $workspace->members()->find($userToAdd->id);
        if ($inWorkspace) {
            return redirect()->back()
                ->withErrors(['id' => 'User is already a member in the workspace'])
                ->withInput($request->all());
        }
        $workspace->members()->attach($userToAdd->id);
        Session::flash('info', 'Member has been added to the workspace');
        return redirect()->action('WorkspaceMembersController@index', ['workspaceId' => $workspaceId]);
    }

    public function remove($memberId)
    {
        $user = Auth::user();
        $member = WorkspaceMember::findOrFail($memberId);
        $workspace = $member->workspace;
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $canRemoveMembers = $userMember->privileges()->where('name', Privilege::REMOVE_MEMBERS)->exists();
        if (!$canRemoveMembers) {
            return abort(403, 'Unauthorized');
        }
        $inWorkspace = (bool) $workspace->workspaceMembers()->find($memberId);
        if (!$inWorkspace) {
            Session::flash('danger', 'Member is not in workspace');
            return redirect()->back();
        }
        $member->delete();
        Session::flash('danger', 'Member has been removed from the workspace');
        return redirect()->action('WorkspaceMembersController@index', ['workspaceId' => $workspace->id]);
    }

    public function updatePrivileges(Request $request, $memberId)
    {
        $member = WorkspaceMember::findOrFail($memberId);
        $user = Auth::user();
        $user_member = WorkspaceMember::where('user_id', $user->id)->where('workspace_id', $member->workspace_id)->first();
        if (!$user_member) {
            return abort(403, 'Unauthorized');
        }

        $new_privileges = $request['new_privileges'];
        if ($user->workspaces()->find($member->workspace_id)) {
            if ($new_privileges) {
                foreach ($new_privileges as $new_privilege) {
                    $member->privileges()->attach($new_privilege);
                }
            }
            $remove_privileges = $request['remove_privileges'];
            if ($remove_privileges) {
                foreach ($remove_privileges as $remove_privilege) {
                    $member->privileges()->detach($remove_privilege);
                }
            }
        }

        $user_privileges = $user_member->privileges->map(function($privilege) { return $privilege->name; })->toArray();

        if (in_array(Privilege::ADD_MEMBERS_SCREENS, $user_privileges)) {
            $new_screens = $request['new_screens'];
            if ($new_screens) {
                foreach ($new_screens as $new_screen) {
                    $member->screens()->attach($new_screen);
                }
            }
        }

        if (in_array(Privilege::REMOVE_MEMBERS_SCREENS, $user_privileges)) {
            $remove_screens = $request['remove_screens'];
            if ($remove_screens) {
                foreach ($remove_screens as $remove_screen) {
                    $member->screens()->detach($remove_screen);
                }
            }
        }
        Session::flash('info', 'Member privileges has been updated successfully.');
        return redirect()->back();
    }

    public function advertisements($memberId)
    {
        $member = WorkspaceMember::findOrFail($memberId);
        $user = Auth::user();
        $user_member = WorkspaceMember::where('user_id', $user->id)->where('workspace_id', $member->workspace_id)->first();
        $privileges = null;
        if ($member->user_id != $user->id) {
            $privileges = $user_member->privileges->map(function($privilege) { return $privilege->name; })->toArray();
            $allowedPrivileges = [
                Privilege::REMOVE_ADVERTISEMENTS,
                Privilege::UPDATE_ADVERTISEMENTS,
            ];
            if (empty(array_intersect($privileges, $allowedPrivileges))) {
                return abort(403, 'Unauthorized');
            }
        }

        $advertisements = Advertisement::where('creator_id', $member->user_id)
            ->where('workspace_id', $member->workspace_id)->paginate();

        return view('workspaces/members/advertisements', compact('member','advertisements', 'privileges'));
    }
}
