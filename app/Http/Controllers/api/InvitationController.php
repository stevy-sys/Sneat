<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function getAllMyDemandeNoAccept()
    {
        try {

            $invitation = Auth::user()->invitations()->where('status',0)->get();
            return response()->json([
                'conversation' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function getAllMyInvitation()
    {
        try {
            $invitation = Auth::user()->invitations()->where('inviteur',Auth::id())->where('status',0)->get();
            return response()->json([
                'conversation' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function accepteAmis(Request $request)
    {
        try {
            $invitation = Invitation::find($request->id_invitation);
            $invitation->update(['status' => 1]);
            return response()->json([
                'conversation' => $invitation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function inviteUser(Request $request)
    {
        try {

            $invit = Auth::user()->invitations()->create([
                'invite' => Auth::id(),
                'status' => 0,
                'invite' => $request->id_destinateur,
            ]);

            return response()->json([
                'conversation' => $invit
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
