<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\api\FriendsController;
use App\Http\Controllers\api\ActualiteController;
use App\Http\Controllers\api\InvitationController;
use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\api\CommentaireController;
use App\Http\Controllers\api\PublicationController;
use App\Models\Publication;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// }); 

// test de git 2

Route::post('login',[AuthController::class,'login']);
Route::post('register',[AuthController::class,'register']);

Route::post('/admin/login',[AdminAuthController::class,'login']);
Route::post('/admin/register',[AdminAuthController::class,'register']);

Route::any('/unauth',function(){
    return response()->json([
        "message" => "unauthentified"
    ],401);
})->name('unauthentified');

Route::middleware('auth:user')->group(function() {
    Route::get('/user',[UserController::class, 'myProfile']);
    Route::get('/otherProfil/{user}',[UserController::class, 'otherProfil']);
    Route::post('/user/complete-profil',[UserController::class, 'completeProfile']);

    // actualite
    Route::get('/actualite',[ActualiteController::class, 'getActualite']);


    //chat
    Route::get('/chat/allConversation',[ChatController::class, 'allConversation']); //liste tout mes conversation
    Route::get('/chat/allDiscussion/{id_conversation?}',[ChatController::class, 'allDiscussion']); //liste tout les message dans une conversation
    Route::post('/chat/sendMessage',[ChatController::class, 'sendMessage']); //envoyer message



    //publication statut
    Route::post('/publier/create',[PublicationController::class, 'publierStatut']); //publiez status
    Route::post('/publier/delete/{id_publication}',[PublicationController::class, 'supprimerStatut']); //supprimer statut
    Route::post('/publier/update/{id_publication}',[PublicationController::class, 'modifier']); // modifier statut
    Route::get('/publier/view/{id_publication}',[PublicationController::class, 'viewStatut']); //voir status



    //groupe
    Route::post('/group/create',[GroupController::class, 'creategroupe']); //creer une groupe
    Route::get('/group/membres/{group_id}',[GroupController::class, 'getMembreGroup']); //tout les user membres groupe
    Route::get('/group',[GroupController::class, 'groupe']); //affiche mes groupe
    Route::post('/invitation/joinGroup',[InvitationController::class, 'joinGroup']); //rejoindre groupe
    Route::post('/invitation/accepteJoinGroup',[InvitationController::class, 'accepteJoinGroup']); //accepte user in groupe
    Route::post('/invitation/all-demande-groupe',[InvitationController::class, 'allDemandeGroup']);
    Route::post('/group/publiez',[PublicationController::class, 'publiezInGroup']);



    //commentaire
    Route::post('/commentaire/create',[CommentaireController::class, 'createCommentaire']);
    Route::post('/commentaire/delete',[CommentaireController::class, 'deleteCommentaire']);
    Route::post('/commentaire/modifier',[CommentaireController::class, 'modifierCommentaire']);

    //reactions




    //invitation
    Route::post('/invitation/invit-amis',[InvitationController::class, 'inviteUserEnAmis']); //inviter une user en amis
    Route::post('/invitation/accept',[InvitationController::class, 'accepteEnAmis']); //accetpe une invitation en amis
    Route::get('/invitation/all-invitation-no',[InvitationController::class, 'getAllMyInvitation']); //tout les invitations que jai envoyer mais pas encore accepter
    Route::get('/invitation/all-invitation-yes',[InvitationController::class, 'getAllMyDemandeNoAccept']); //tout les invitation que je recois mes jai pas encore accepter


    // friends
    Route::get('/friends/all-amis',[FriendsController::class, 'getAllFriends']); //tout mes amis
    Route::post('/friends/retirer/{user_id}',[FriendsController::class, 'retirer']); //retirer amis
});

Route::middleware('auth:admin')->group(function() {
    Route::get('/admin/home',[HomeController::class, 'index']);
});