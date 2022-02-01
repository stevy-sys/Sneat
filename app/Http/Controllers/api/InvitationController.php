<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function getAllMyDemandeNoAccept()
    {
        try {

            $invitation = Auth::user()->invitation()->where('status',0)->get();
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
            $invitation = Auth::user()->invitation()->where('inviteur',Auth::id())->where('status',0)->get();
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

            $invit = Auth::user()->invitation()->create([
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
