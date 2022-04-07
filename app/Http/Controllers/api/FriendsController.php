<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Friends;
use App\Models\Invitation;
use App\Models\MembreGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    public function suggestionAMis()
    {
        $suggestion = [] ;
        $tableauAmis = getTableauAmis();
        $groupe = MembreGroup::where('user_id',Auth::id())->whereHas('group',function ($q) use ($tableauAmis)
        {
            $q->wherehas('membresGroupe',function ($q) use ($tableauAmis){
                $q->whereNotIn('user,id',$tableauAmis);
            });
        });
        $groupe->membresGroupe()->whereHas('user',function ($q){
            $q->whereHas('friends');
        })->where('user_id',Auth::id());

        // $suggestion = User::whereHas('');
    }

    public function getAllFriends()
    {
        try {
            $user = Friends::with('user_friend')->where('user_id',Auth::id())->get();
            return response()->json([
                'friends' => $user
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function retirer($user_id)
    {
        try {
            Friends::where('user_id',Auth::id())->where('friend_id',$user_id)->delete();
            Friends::where('user_id',$user_id)->where('friend_id',Auth::id())->delete();
            return response()->json([
                'message' => 'friends retirer'
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
