<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function partageInMyMur(Publication $publication,Request $request)
    {
        $share = $publication->sharable()->create([
            'user_id' => Auth::id(),
        ]);
        $publicable = $share->publicable()->create([
            'description' => $request->description,
            'user_id' => Auth::id()
        ]);

        $publicable->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }
}
