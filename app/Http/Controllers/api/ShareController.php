<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/share/publication/myMur/{publication}",
     *      operationId="partageInMyMur",
     *      tags={"Publications"},
     *      summary="partager et publier un statut",
     *      description="Publier un statut en partagant une statut d-un autre utilisateur.",
     *      @OA\RequestBody(
     *          description="Données du statut à publier",
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="Un status de John Doe que je vient de partager"
     *              )
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="publication",
     *          in="path",
     *          required=true,
     *          description="ID du publication",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Statut publié avec succès",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function partageInMyMur(Publication $publication,Request $request)
    {
        $share = $publication->sharable()->create([
            'user_id' => Auth::id(),
        ]);
        $publicable = $share->publicable()->create([
            'description' => $request->description,
            'user_id' => Auth::id()
        ]);

        $publicable->actualites()->create();
        return response()->json([
            'data' => $publication
        ],201);
    }
}
