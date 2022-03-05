<?php

use App\Models\Friends;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getTableauAmis')) {
    function getTableauAmis()
    {
        $amis = [];
        $amis[] = Auth::id();
        $firends = Friends::where('user_id',Auth::id())->get(['friend_id']);
        foreach ($firends as $amis_id) {
            $amis[] = $amis_id->friend_id;
        }
        return $amis ;
    }
}