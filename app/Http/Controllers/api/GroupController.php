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


    /**
     * @OA\Post(
     *      path="/api/group/create",
     *      operationId="creategroupe",
     *      tags={"Group"},
     *      summary="Creation d'une groupe",
     *      description="Creation d'une groupe ou je suis admin",
     *      @OA\RequestBody(
     *          description="Données de groupe a creer",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="nom de groupe"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Creation d'une groupe",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
    */
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

    /**
     * @OA\Get(
     *      path="/api/group/membres/{group_id}",
     *      operationId="getMembreGroup",
     *      tags={"Group"},
     *      summary="Obtenir les membres d'un groupe",
     *      description="Retourne les membres d'un groupe en fonction de l'ID du groupe.",
     *      @OA\Parameter(
     *          name="group_id",
     *          in="path",
     *          required=true,
     *          description="ID du groupe",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Liste des membres du groupe",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function getMembreGroup($group_id)
    {
        return response()->json([
            'membre' => MembreGroup::with(['user','role'])->where('group_id',$group_id)->get()
        ],201);
    }

    /**
     * @OA\Get(
     *      path="/api/group",
     *      operationId="groupe",
     *      tags={"Group"},
     *      summary="Mes groupes",
     *      description="Mes groupe ou je suis membre",
     *      @OA\Response(
     *          response=200,
     *          description="Liste de mes groupes",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
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
