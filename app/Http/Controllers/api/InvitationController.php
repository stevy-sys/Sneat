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


    /**
     * @OA\Get(
     *      path="/api/invitation/all-invitation-yes",
     *      operationId="getAllMyDemandeNoAccept",
     *      tags={"Invitations"},
     *      summary="Mes invitation que je recois non lue",
     *      description="tout les invitation que je recois mais jai pas encore accepter",
     *      @OA\Response(
     *          response=200,
     *          description="Mes invitation que je recois non lue",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/invitation/all-invitation-no",
     *      operationId="getAllMyInvitation",
     *      tags={"Invitations"},
     *      summary="Invitation que jai envoyer pas de reponse",
     *      description="tout les invitations que jai envoyer mais pas encore accepter",
     *      @OA\Response(
     *          response=200,
     *          description="Invitation que jai envoyer pas de reponse",
     *          @OA\JsonContent(
     *              type="object"
     *          )
     *      )
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/invitation/accept",
     *      operationId="accepteEnAmis",
     *      tags={"Invitations"},
     *      summary="accepte une demande d'amis qu on m'a envoyer",
     *      description="Accepte une demande d'amis",
     *      @OA\RequestBody(
     *          description="ID de user a inviter",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="id_invitation",
     *                  type="integer",
     *                  example="id de invitation"
     *              ),
     *              @OA\Property(
     *                  property="friend_id",
     *                  type="integer",
     *                  example="id de user qui m'a inviter"
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="accepte user en amis",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/invitation/invit-amis",
     *      operationId="inviteUserEnAmis",
     *      tags={"Invitations"},
     *      summary="Envoyer une demande d'amis",
     *      description="Envoyer une demande d'amis",
     *      @OA\RequestBody(
     *          description="ID de user a inviter",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="id_destinateur",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Invitation user en amis",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/invitation/joinGroup",
     *      operationId="joinGroup",
     *      tags={"Group"},
     *      summary="Rejoindre une groupe",
     *      description="Envoyer une invitation dans une groupe pour rejoindre ce groupe",
     *      @OA\RequestBody(
     *          description="ID de groupe",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="id_group",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Rejoindre une groupe",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/invitation/accepteJoinGroup",
     *      operationId="accepteJoinGroup",
     *      tags={"Group"},
     *      summary="Accepte un membre en groupe",
     *      description="Accepter une invitation de rejoindre dans un groupe , action fait par admin",
     *      @OA\RequestBody(
     *          description="ID de groupe",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="id_invitation",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="id_group",
     *                  type="integer",
     *                  example="1"
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Accepte un membre en groupe",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/invitation/all-demande-groupe",
     *      operationId="allDemandeGroup",
     *      tags={"Group"},
     *      summary="Tout les invitation en cours dans groupe",
     *      description="Invitation pas encore accepter pour rejoindre le groupe",
     *      @OA\RequestBody(
     *          description="ID de groupe",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="id_group",
     *                  type="integer",
     *                  example="1"
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Invitation en cours",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
    public function allDemandeGroup(Request $request)
    {
        try {
            return response()->json([
                'invitation' =>  Invitation::where('invitable_type','App\Models\Group')->where('status',0)->where('invitable_id',$request->id_group)->get()
            ],201);
           
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
