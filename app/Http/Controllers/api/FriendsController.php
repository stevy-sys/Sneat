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
    public function suggestionAMis()
    {


        // Obtenez l'ID de l'utilisateur connecté
        $userId = Auth::user()->id;

        // Obtenez la liste des amis de l'utilisateur actuel
        $mesAmisIds = Friends::where('user_id', $userId)
            ->orWhere('friend_id', $userId)
            ->pluck('user_id', 'friend_id')
            ->toArray();

        // Ajoutez l'ID de l'utilisateur actuel à la liste
        $mesAmisIds[$userId] = $userId;

        // Retirez l'ID de l'utilisateur actuel s'il est présent dans la liste
        if (isset($mesAmisIds[$userId])) {
            unset($mesAmisIds[$userId]);
        }

        // Obtenez les amis de mes amis qui ne sont pas déjà mes amis
        $amisDeMesAmisIds = Friends::whereIn('user_id', array_keys($mesAmisIds))
            ->orWhereIn('friend_id', array_keys($mesAmisIds))
            ->whereNotIn('user_id', $mesAmisIds)
            ->whereNotIn('friend_id', $mesAmisIds)
            ->pluck('user_id', 'friend_id')
            ->toArray();

        // Obtenez les membres des groupes auxquels l'utilisateur appartient mais qui ne sont pas ses amis
        $groupMembersIds = MembreGroup::whereIn('group_id', function ($query) use ($userId) {
            $query->select('group_id')
                ->from('membre_groups')
                ->where('user_id', $userId);
        })
            ->whereNotIn('user_id', $mesAmisIds)
            ->pluck('user_id')
            ->toArray();

        // Obtenez la liste finale des utilisateurs suggérés
        $userIdsSuggérés = array_merge(array_values($amisDeMesAmisIds), $groupMembersIds);
        $userIdsSuggérés = array_unique($userIdsSuggérés);

        // Paginez les résultats avec 5 utilisateurs par page
        $page = request('page', 1); // Obtenez le numéro de la page depuis la requête (par défaut 1)
        $perPage = 5; // Nombre d'utilisateurs par page

        // Obtenez les détails des utilisateurs suggérés avec amis en commun
        $utilisateursSuggérés = User::whereIn('id', $userIdsSuggérés)
            ->whereNotIn('id', array_keys($mesAmisIds))
            ->inRandomOrder()
            ->paginate($perPage, ['*'], 'page', $page);
            foreach ($utilisateursSuggérés as $user) {
                $user['amisCommun'] = getAmisCommun($user,Auth::user());
            }

        // Retournez les utilisateurs suggérés paginés au format JSON
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
            $user = Friends::with('user_friend')->where('user_id', Auth::id())->get();
            return response()->json([
                'friends' => $user
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
