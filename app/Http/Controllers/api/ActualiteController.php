<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Actualites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActualiteController extends Controller
{
    public function getActualite()
    {
        $amis_id = getTableauAmis();
        $actualite = Actualites::whereHas(
            'actualable',function ($query) use ($amis_id){
               $query->whereHasMorph('publicable',['App\Models\User'],function ($q) use ($amis_id){
                   $q->whereIn('id',$amis_id);
               })->orWhereHasMorph('publicable',['App\Models\Group'],function ($query){
                   $query->whereHas('membresGroupe',function ($query){
                       $query->where('user_id',Auth::id());
                   });
               });            
            }
        )
        ->with([
            'actualable.user',
            'actualable.publicable',
            'actualable.media',
            'actualable.commentaires.user'
        ])->get()->pluck('actualable');
        return response()->json([
            'data' => $actualite
        ],201);
    }

}
