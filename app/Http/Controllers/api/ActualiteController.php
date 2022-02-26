<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Actualites;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    public function getActualite()
    {

        $actualite = Actualites::with(['actualable.user','actualable.media','actualable.commentaires.user'])->get();
        return response()->json([
            'data' => $actualite
        ],201);
    }
}
