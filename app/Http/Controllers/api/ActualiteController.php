<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Actualites;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="sneat",
 *     version="1.0.0",
 *     description="Description de votre API",
 *     @OA\Contact(
 *         email="contact@api.com",
 *         name="Nom du contact"
 *     ),
 *     @OA\License(
 *         name="Licence de l'API",
 *         url="URL de la licence"
 *     )
 * )
 */
class ActualiteController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/actualite",
     *      operationId="getActualite",
     *      tags={"Actualite"},
     *      summary="Obtenir la liste des actualités",
     *      description="Retourne la liste des publication ou partage de mes amis et publication dans des groupe ou je juis inscrit",
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="pagination",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *         @OA\Response(
     *          response=200,
     *          description="Liste des actualités",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function getActualite()
    {
        $amis_id = Auth::user()->friends->pluck('id');
        $actualite = Actualites::whereHas(
            'actualable',
            function ($query) use ($amis_id) {
                $query->WhereHasMorph('publicable', ['App\Models\User'], function ($q) use ($amis_id) {
                    $q->whereIn('id', $amis_id);
                })->orWhereHasMorph('publicable', ['App\Models\Group'], function ($query) {
                    $query->whereHas('membresGroupe', function ($query) {
                        $query->where('user_id', Auth::id());
                    });
                })->orWhereHasMorph('publicable', ['App\Models\Shares'], function ($query) use ($amis_id) {
                    $query->whereIn('user_id', $amis_id);
                });
            },
        )->with([
                'actualable.user',
                'actualable.publicable.sharable.user',
                'actualable.publicable.sharable.media',
                'actualable.publicable.sharable.commentaires.user',
                'actualable.media',
                'actualable.commentaires.user'
            ])->orderBy('created_at','desc')->paginate(5);
            
        return response()->json([
            'data' => $actualite
        ], 201);
    }
}
