<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScreensController extends Controller
{
    public function index() {
        $screens = Auth::user()->screens()->paginate();
        return view('screens/index', compact('screens'));
    }

    public function show($id) {
        $screen = Auth::user()->screens()->findOrFail($id);
        if (!$screen) {
            return view('404');
        }
        $screen['api_key'] = $screen->createToken('SuperSecretSecretPhrase')->accessToken;
        return view('screens/show', compact('screen'));
    }

    public function create() {
        return view('screens/create');
    }

    public function store(Request $request) {
        $request->validate([
            'count' => 'required|numeric|between:1,20',
        ]);

        $count = $request['count'];
        $user = Auth::user();
        for ($i = 0; $i < $count; $i++) {
            $user->screens()->create(['type' => 'private']);
        }
        return redirect()->action('ScreensController@index');
    }

    public function edit($id) {
        $screen = Auth::user()->screens()->findOrFail($id);
        if (!$screen) return response()->view('404', null, 404);

        return view('screens/edit', compact('screen'));
    }

    public function update($id, Request $request) {
        $request->validate([
            'type' => 'required',
            'location' => 'required',
            'details' => 'required',
        ]);
        $screen = Auth::user()->screens()->findOrFail($id);
        if (!$screen) {
            return view('404');
        }

        $screen->update([
            'type' => $request['type'],
            'location' => $request['location'],
            'details' => $request['details']
        ]);
        return redirect()->action('ScreensController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $screen = Auth::user()->screens()->findOrFail($id);
        if (!$screen) return response()->view('404', null, 404);

        $screen->delete();
        return redirect()->action('ScreensController@index');
    }

    public function downloadConfigFile($id)
    {
        $screen = Auth::user()->screens()->findOrFail($id);
        if (!$screen) {
            return view('404');
        }
        $key = $screen->createToken('SuperSecretSecretPhrase')->accessToken;
        return response($key)
            ->withHeaders([
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-store, no-cache',
                'Content-Disposition' => 'attachment; filename="config.con',
            ]);
    }
}
