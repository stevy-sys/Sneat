<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\api\AppController;
use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\FriendsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login',[authController::class,'login']);
Route::post('register',[authController::class,'register']);

Route::post('/admin/login',[AdminAuthController::class,'login']);
Route::post('/admin/register',[AdminAuthController::class,'register']);

Route::any('/unauth',function(){
    return response()->json([
        "message" => "unauthentified"
    ],401);
})->name('unauthentified');

Route::middleware('auth:user')->group(function() {
    Route::get('/user',[AppController::class, 'profile']);


    //chat
    Route::get('/chat/allConversation',[ChatController::class, 'allConversation']);
    Route::get('/chat/allDiscussion/{conversation}',[ChatController::class, 'allDiscussion']);
    Route::post('/chat/sendMessage/',[ChatController::class, 'sendMessage']);
    Route::post('/chat/create-conversation',[ChatController::class, 'createConversation']);


    //publication




    //commentaire




    //reactions




    //invitaiton
    Route::post('/invitation/invit-amis',[FriendsController::class, 'inviteUser']);
    Route::post('/invitation/accept',[FriendsController::class, 'accepteAmis']);

});

Route::middleware('auth:admin')->group(function() {
    Route::get('/admin/home',[HomeController::class, 'index']);
});