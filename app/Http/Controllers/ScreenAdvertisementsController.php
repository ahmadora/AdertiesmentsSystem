<?php

namespace App\Http\Controllers;

use App\Helpers\FilesHelper;
use App\Screen;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ScreenAdvertisementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $screenId
     * @return \Illuminate\Http\Response
     */
    public function index($screenId)
    {
        $screen = Screen::findOrFail($screenId);
        $user = Auth::user();
        if ($screen->owner_id != $user->id) {
            return abort(403, 'Unauthorized');
        }
        $advertisements = $screen->advertisements()->paginate();
        return view('advertisements/index', compact('advertisements'));
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function getScreenAdvertisements() {
        $advertisements = Auth::user()->advertisements()
            ->where('expires_at', '>', Carbon::now())
            ->select('advertisements.id',
                'advertisements.url',
                'advertisements.priority',
                'advertisements.expires_at')->get()
            ->map(function($advertisement) {
                return [
                    'id' => $advertisement->id,
                    'url' => FilesHelper::getFileURL(FilesHelper::ADVERTISEMENT, $advertisement->url),
                    'priority' => $advertisement->priority,
                    'expires_at' => $advertisement->expires_at,
                ];
            });
        return response()->json($advertisements);
    }
}
