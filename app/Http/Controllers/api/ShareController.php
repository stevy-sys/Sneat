<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    public function partagePublicationInMyMur(Publication $publication,Request $request)
    {
        $share = $publication->sharable()->create([
            'user_id' => Auth::id(),
        ]);
        $share->publicable()->create([
            'description' => $request->description,
            'user_id' => Auth::id()
        ]);

        $share->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }
}
