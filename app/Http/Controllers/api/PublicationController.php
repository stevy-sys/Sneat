<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        
        if ($request->file) {
            $media = $this->decodebase64($request->file);
            $publication->media()->create([
                'file' => $media['path'],
                'type' => $media['type'],
            ]);
        }
        
        $publication->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }

    public function supprimerStatut($id_publication)
    {
        Publication::find($id_publication)->delete();
        return response()->json([
            'data' => 'publication delete'
        ],201);
    }

    public function modifier($id_publication,Request $request)
    {
        $publication = Publication::find($id_publication) ;
        $publication->update([
            'description' => $request->description
        ]);
        return response()->json([
            'data' => 'publication delete'
        ],201);
    }

    public function viewStatut($id_publication)
    {
        $publication = Publication::with(['user','commentaires.user'])->where('id',$id_publication)->first();
        return response()->json([
            'data' => $publication
        ],201);
    }

    private function decodebase64($data)
    {
        $image_64 = $data; //your base64 encoded data
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid() . '.' . $extension;
        $type = $this->detectType($extension);

        Storage::disk('publication')->put($imageName, base64_decode($image));
        return [
            'path' => $imageName ,
            'type' => $type
        ];
    }
    

    private function detectType($file)
    {
        $image = ['jpg', 'jpeg', 'png'];
        $video = ['mp4', 'avi'];
        $is_image = in_array($file, $image);
        $is_video = in_array($file, $video);

        if (!$is_video && !$is_image) {
            return 'file';
        }

        if ($is_image) {
            return 'image';
        }
        if ($is_video) {
            return 'video';
        }
    }
}
