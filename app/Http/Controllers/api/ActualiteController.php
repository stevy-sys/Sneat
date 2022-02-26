<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Actualites;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    public function getActualite()
    {

        $actualite = Actualites::with(['actualable.user.media','actualable.media','actualable.commentaires'])->get();
        return response()->json([
            'data' => $actualite
        ],201);
    }
}
