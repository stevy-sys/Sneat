<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Friends;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\MembreGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/friends/suggestion",
     *      operationId="suggestionAMis",
     *      tags={"Friends"},
     *      summary="Recupere tout les suggestion d'amis",
     *      description="Recupere tout les user suggerer , user meme groupe que moi et amis de mes amis",
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="pagination",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Recupere tout les suggestion d'amis",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function suggestionAmis()
    {

        // Obtenez les amis de l'utilisateur actuel
        $mesAmisIds = Auth::user()->friends->pluck('id');

        // Obtenez les amis de mes amis qui ne sont pas déjà mes amis
        $amisDeMesAmisIds = User::whereHas('friends', function ($query) use ($mesAmisIds) {
            $query->whereIn('friend_id', $mesAmisIds);
        })
            ->whereNotIn('id', $mesAmisIds)
            ->pluck('id')
            ->toArray();

        // Paginez les résultats avec 5 utilisateurs par page
        $utilisateursSuggérés = User::whereIn('id', $amisDeMesAmisIds)
            ->amisCommun(Auth::user())
            ->where('id','<>',Auth::id())
            ->paginate(5);
        return response()->json(['suggestions_amis' => $utilisateursSuggérés]);
    }

    /**
     * @OA\Get(
     *      path="/api/friends/all-amis",
     *      operationId="getAllFriends",
     *      tags={"Friends"},
     *      summary="Recupere tout les amis",
     *      description="Recupere tout les amis que jai accepter",
     *      @OA\Response(
     *          response=200,
     *          description="Recupere tout les amis",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function getAllFriends()
    {
        try {
            return response()->json([
                'friends' => Auth::user()->friends
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/friends/retirer/{user_id}",
     *      operationId="retirer",
     *      tags={"Friends"},
     *      summary="Retirer une amis",
     *      description="Retirer une amis dans ma liste d'amis",
     *      @OA\Parameter(
     *          name="user_id",
     *          in="path",
     *          required=true,
     *          description="ID du user",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Retirer une amis",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      ),
     * )
     */
    public function retirer($user_id)
    {
        try {
            Friends::where('user_id', Auth::id())->where('friend_id', $user_id)->delete();
            Friends::where('user_id', $user_id)->where('friend_id', Auth::id())->delete();
            return response()->json([
                'message' => 'friends retirer'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
