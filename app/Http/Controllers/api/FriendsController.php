<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Friends;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    

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
