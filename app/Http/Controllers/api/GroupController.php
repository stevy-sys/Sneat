<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\MembreGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function creategroupe(Request $request)
    {
        $newGroup = Auth::user()->groups()->create([
            'name' => $request->name
        ]);

        $newGroup->membresGroupe()->create([
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'conversation' => 'group created'
        ],201);
    }

    public function getMembreGroup($group_id)
    {
       return response()->json([
            'conversation' => MembreGroup::with('user')->where('groupe_id',$group_id)->get()->pluck('user')
        ],201);
    }
}
