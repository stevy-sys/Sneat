<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Actualites;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    public function getActualite()
    {
        $amis_id = getTableauAmis();
        $actualite = Actualites::whereHas(
            'actualable.user' , function ($query) use ($amis_id)
            {
              $query->whereIn('id',$amis_id);
            },
        )->with([
            'actualable.user',
            'actualable.media',
            'actualable.commentaires.user'
        ])->get();
        
        return response()->json([
            'data' => $actualite
        ],201);
    }

}
