<?php

namespace App\Http\Controllers;

use App\Privilege;
use App\Workspace;
use App\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workspaces = Auth::user()->workspaces()->paginate();
        return view('workspaces/index', compact('workspaces'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('workspaces/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'details' => 'required',
        ]);

        $user = Auth::user();
        $workspace = $user->workspaces()->create([
            'title' => $request['title'],
            'details' => $request['details'],
        ]);
        $workspace->members()->attach($user->id);
        $member = WorkspaceMember::where('user_id', $user->id)->where('workspace_id', $workspace->id)->first();
        $member->addPrivileges(Privilege::getAllPrivileges());
        return redirect()->action('WorkspacesController@show', ['id' => $workspace->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $workspace = Workspace::findOrFail($id);
        $user = Auth::user();
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $privileges = $userMember->privileges->map(function($privilege) { return $privilege->name; })->toArray();
        $is_owner = $user->id == $workspace->owner_id;
        return view('workspaces/show', compact('workspace', 'privileges', 'is_owner'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $workspace = Auth::user()->workspaces()->findOrFail($id);

        return view('workspaces/edit', compact('workspace'));
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
        $workspace = Auth::user()->workspaces()->findOrFail($id);

        $request->validate([
            'title' => 'required',
            'details' => 'required',
        ]);

        $workspace->update([
            'title' => $request['title'],
            'details' => $request['details'],
        ]);
        return redirect()->action('WorkspacesController@show', ['id' => $workspace->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $workspace = Auth::user()->workspaces()->findOrFail($id);

        $workspace->delete();
        return redirect()->action('WorkspacesController@index');
    }
}
