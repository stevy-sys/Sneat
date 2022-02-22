<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

            $invitation = $this->auth->invitations()->where('invite',Auth::id())->where('status',0)->get();
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
            $invitation = $this->auth->invitations()->where('inviteur',Auth::id())->where('status',0)->get();
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
}
