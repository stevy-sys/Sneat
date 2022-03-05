<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Friends;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\RoleUser;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    protected $auth ;
    public function __construct() {

        $this->auth = User::find(Auth::id());
    }

    public function getAllMyDemandeNoAccept()
    {
        try {

            $invitation = Invitation::with('inviteur')->where('invitable_type','App\Models\User')->where('invite',Auth::id())->where('status',0)->get();
            return response()->json([
                'invitation' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function getAllMyInvitation()
    {
        try {
            $invitation = Invitation::with('invite')->where('invitable_type','App\Models\User')->where('inviteur',Auth::id())->where('status',0)->get();
            return response()->json([
                'data' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function accepteEnAmis(Request $request)
    {
        try {
            $invitation = Invitation::find($request->id_invitation);
            $invitation->update(['status' => 1]);
            Friends::create([
                'friend_id'=>$request->friend_id,
                'user_id' => Auth::id()
            ]);
            Friends::create([
                'friend_id'=>Auth::id(),
                'user_id'=>$request->friend_id
            ]);
            return response()->json([
                'invitation' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function inviteUserEnAmis(Request $request)
    {
        try {

            $invit = $this->auth->invitations()->create([
                'inviteur' => Auth::id(),
                'status' => 0,
                'invite' => $request->id_destinateur,
            ]);

            return response()->json([
                'invitation' => $invit
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function joinGroup(Request $request)
    {
        try {
            $groupe = Group::find($request->id_group);
            $invitations = $groupe->invitable()->create([
                'invite' => Auth::id()
            ]);
            return response()->json([
                'invitation' => $invitations
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function accepteJoinGroup(Request $request)
    {
        try {
            $invitation = Invitation::find($request->id_invitation);
            $invitation->update([
                'status' => 1
            ]);
            $groupe = Group::find($request->id_group);
            $groupe->membresGroupe()->create([
                'user_id' => $invitation->invite,
                'role_id' => RoleUser::where('type','membre')->first()->id
            ]);
            return response()->json([
                'invitation' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
