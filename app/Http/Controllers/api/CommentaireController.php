<?php

namespace App\Http\Controllers\api;

use App\Models\Commentaire;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentaireController extends Controller
{
    public function createCommentaire(Request $request)
    {
        $publication = Publication::find($request->publication_id);
        $commentaire = $publication->commentaires()->create([
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);
        return response()->json([
            'data' => $commentaire
        ],201);
    }

    public function deleteCommentaire($id_commentaire)
    {
        Commentaire::find($id_commentaire)->delete();
        return response()->json([
            'data' => 'commentaire delete'
        ],201);
    }

    public function modifierCommentaire($id_commentaire,Request $request)
    {
        $commentaire =  Commentaire::find($id_commentaire) ;
        $commentaire->update([
            'message' => $request->message
        ]);
        return response()->json([
            'data' => $commentaire
        ],201);
    }
}
