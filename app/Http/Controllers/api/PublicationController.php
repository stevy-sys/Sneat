<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicationController extends Controller
{
    protected $auth ;
    public function __construct() {

        $this->auth = User::find(Auth::id());
    }

    public function publierStatut (Request $request)
    {
        $publication = $this->auth->publicationStatus()->create([
            'user_id' => Auth::id(),
            'description' => $request->description
        ]);
        $publication->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }

    public function supprimerStatut($id_publication)
    {
        Publication::find($id_publication)->delete();
        return response()->json([
            'message' => 'publication delete'
        ],201);
    }

    public function modifier($id_publication,Request $request)
    {
        $publication = Publication::find($id_publication) ;
        $publication->update([
            'description' => $request->description
        ]);
        return response()->json([
            'message' => 'publication delete'
        ],201);
    }

    public function viewStatut($id_publication)
    {
        $publication = Publication::with(['user','commentaires.user'])->where('id',$id_publication)->first();
        return response()->json([
            'data' => $publication
        ],201);
    }
}
