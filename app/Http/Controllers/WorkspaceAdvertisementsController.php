<?php

namespace App\Http\Controllers;

use App\Events\AdvertisementAdded;
use App\Helpers\FilesHelper;
use App\Privilege;
use App\ScreenAdvertisement;
use App\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WorkspaceAdvertisementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $workspaceId
     * @return \Illuminate\Http\Response
     */
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
            Privilege::UPDATE_ADVERTISEMENTS,
            Privilege::REMOVE_ADVERTISEMENTS,
        ];
        $canViewMembers = !empty(array_intersect($privileges, $allowedPrivileges));
        if (!$canViewMembers) {
            return abort(403, 'Unauthorized');
        }
        $advertisements = $workspace->advertisements()->paginate();
        return view('workspaces/advertisements/index', compact('advertisements', 'privileges'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param $workspaceId
     * @return \Illuminate\Http\Response
     */
    public function ownIndex($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = $workspace->members()->find($user->id);
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $advertisements = $user->advertisements()->where('workspace_id', $workspaceId)->paginate();
        return view('workspaces/advertisements/own', compact('advertisements', 'workspaceId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $workspaceId
     * @return \Illuminate\Http\Response
     */
    public function create($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = $workspace->workspaceMembers()->where('user_id', $user->id)->first();
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $screens = $userMember->screens()->get();
        return view('workspaces/advertisements/create', compact('screens', 'workspaceId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $workspaceId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $user = Auth::user();
        $userMember = $workspace->members()->find($user->id);
        if (!$userMember) {
            return abort(403, 'Unauthorized');
        }
        $file = $request->file('file');
        $file_name = FilesHelper::saveFile($file, FilesHelper::ADVERTISEMENT);
        $advertisement = $user->advertisements()->create([
            'title' => $request['title'],
            'url' => $file_name,
            'priority' => $request['priority'],
            'expires_at' => $request['expires_at'],
            'workspace_id' => $workspaceId,
        ]);
        $screens = $request['screens'];
        foreach($screens as $screen) {
            $advertisement->screens()->attach($screen);
            event(new AdvertisementAdded($screen, [
                'id' => $advertisement->id,
                'url' => FilesHelper::getFileURL(FilesHelper::ADVERTISEMENT, $advertisement->url),
                'priority' => $advertisement->priority,
                'expires_at' => $advertisement->expires_at,
            ]));
        }
        Session::flash('info', 'Advertisement has been created successfully.');
        return redirect()->action('WorkspaceAdvertisementsController@ownIndex', ['workspaceId' => $workspaceId]);
    }
}
