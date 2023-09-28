<?php

use App\Models\User;
use App\Models\Friends;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getTableauAmis')) {
    function getTableauAmis()
    {
        $amis = [];
        $amis[] = Auth::id();
        $firends = Friends::where('user_id',Auth::id())->get(['friend_id']);
        foreach ($firends as $amis_id) {
            if ($amis_id->friend_id !== Auth::id()) {
                $amis[] = $amis_id->friend_id;
            }
        }
        return $amis ;
    }
}


if (!function_exists('getAmisCommun')) {
    function getAmisCommun($user,$auth)
    {
        // Obtenez les amis de l'utilisateur authentifié
        $amisUtilisateurAuth = [];
        $firends = Friends::where('user_id',$auth->id)->get(['friend_id']);
        foreach ($firends as $amis_id) {
            if ($amis_id->friend_id !== $auth->id) {
                $amisUtilisateurAuth[] = $amis_id->friend_id;
            }
        }

        // Obtenez les amis de l'autre utilisateur
        $amisAutreUtilisateur = [];
        $firends = Friends::where('user_id',$user->id)->get(['friend_id']);
        foreach ($firends as $amis_id) {
            if ($amis_id->friend_id !== $user->id) {
                $amisAutreUtilisateur[] = $amis_id->friend_id;
            }
        }
        // Obtenez les amis en commun
        $amisEnCommunIds = array_intersect($amisUtilisateurAuth, $amisAutreUtilisateur);

        // Obtenez les détails des amis en commun
        $amisEnCommun = User::whereIn('id', $amisEnCommunIds)->get();
        return $amisEnCommun;
    }
}