<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    protected $auth ;
    public function __construct() {

        $this->auth = User::find(Auth::id());
    }
    
    public function myProfile()
    {
        try {
            $profil = $this->auth->load('profil.media');
            return response()->json([
                'conversation' => $profil
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }


    public function completeProfile(Request $request)
    {
        try {
            $data = $request->all();
            $user = User::find(Auth::id());
            $user->profil->update($data);
            return response()->json([
                'profil' => $user->profil
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function otherProfil(User $user)
    {
        $user = $user->load('profil.media');
        return response()->json([
            'user' => $user
        ],201);
    }
}
