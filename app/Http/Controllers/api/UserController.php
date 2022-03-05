<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $auth;
    public function __construct()
    {

        $this->auth = User::find(Auth::id());
    }

    public function myProfile()
    {
        try {
            $profil = $this->auth->load('profil.media');
            return response()->json([
                'data' => $profil
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    


    public function completeProfile(Request $request)
    {
        try {

            $user = User::find(Auth::id());

            $user->profil->update([
                'username' => $request->username,
                'sexe' => $request->sexe,
                'description' => $request->description,
                'date_naissance' => $request->date_naissance,
                'addresse' => $request->addresse
            ]);

            if ($request->photo_profil) {
                $media = $this->decodebase64($request);
                $exist = $user->media()->first();
                $user->media()->create([
                    'file' => $media['path'],
                    'type' => $media['type'],
                    'active' => $exist ? false : true
                ]);
            }
            return response()->json([
                'profil' => $user->profil
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function otherProfil(User $user)
    {
        $user = $user->load('profil.media');
        return response()->json([
            'user' => $user
        ], 201);
    }

    private function decodebase64($request)
    {
        $image_64 = $request->photo_profil;; //your base64 encoded data
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid() . '.' . $extension;
        $type = $this->detectType($extension);

        Storage::disk('profil')->put($imageName, base64_decode($image));
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
