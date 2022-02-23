<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MembreConversation;
use App\Models\User;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    protected $auth ;
    public function __construct() {

        $this->auth = User::find(Auth::id());
    }

    private function createConversation($request)
    {
        try {
            
            $conversation = Conversation::create([
                'name' => $request->name ? $request->name : null ,
                'type' => $request->type
            ]);
                
            return $conversation ;
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    private function createMembre($conversation,$id_user)
    {
        $conversation = Conversation::find($conversation);
        $conversation->membres()->create([
            'user_id' => Auth::id()
        ]);
        $conversation->membres()->create([
            'user_id' => $id_user
        ]);
    }

    private function verification($user_id)
    {
        try {
            $user = User::find($user_id);
            $idConversation = null;
            $lists = MembreConversation::with(['conversation.membres' => function($q) use ($user){
                $q->where('user_id',$user->id);
            }])->where('user_id',Auth::id())->get()->pluck('conversation.membres');

            foreach ($lists as $list) {
                if (count($list) > 0) {
                    $idConversation = $list[0]->conversation_id ;
                }
            }
            return $idConversation ;       
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }



    public function AllConversation()
    {
        try {
            $allConversation = Conversation::whereHas('membres',function($query){
                $query->where('user_id',Auth::id());
            })->with(['latestMessage','whoDiscuss'])->get();
            return response()->json([
                'conversation' => $allConversation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }


    public function allDiscussion($id_conversation = null, Request $request)
    {
        try {
            
            if (!$id_conversation) {
                $id_conversation = $this->verification($request->user_id);
                if (!$id_conversation) {
                    return response()->json([
                        'conversation' => null
                    ],201);
                }
            }

            $discussion = Message::with('user')->where('conversation_id',$id_conversation)->get();
            return response()->json([
                'conversation' => $discussion
            ],201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
        
    }

   

    public function sendMessage(Request $request)
    {
        try {

            $id_conversation = $this->verification($request->id_user);
            if (!$id_conversation) {
                $conversation = $this->createConversation($request);
                $this->createMembre($conversation->id,$request->id_user);
                $message =  $this->auth->Messages()->create([
                    'conversation_id' => $conversation->id,
                    'message' => $request->messages
                ]);
            }else{
                $message =  $this->auth->Messages()->create([
                    'conversation_id' => $id_conversation,
                    'message' => $request->messages
                ]);
            }

            return response()->json([
                'conversation' => $message
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
