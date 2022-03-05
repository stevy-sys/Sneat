<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\MembreGroup;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    protected $auth ;
    public function __construct() {

        $this->auth = User::find(Auth::id());
    }


    public function creategroupe(Request $request)
    {
        $newGroup = $this->auth->groups()->create([
            'name' => $request->name
        ]);
        
        $newGroup->membresGroupe()->create([
            'user_id' => $newGroup->user_id,
            'role_id' => RoleUser::where('type','admin')->first()->id
        ]);

        return response()->json([
            'message' => $newGroup
        ],201);
    }

    public function getMembreGroup($group_id)
    {
        return response()->json([
            'membre' => MembreGroup::with('user')->where('groupe_id',$group_id)->get()->pluck('user')
        ],201);
    }

    public function groupe()
    {
        $groupe = Group::whereHas('membresGroupe',function($q){
            $q->where('user_id',Auth::id());
        })->get();

        return response()->json([
            'groupe' => $groupe
        ],201);
    }
}
