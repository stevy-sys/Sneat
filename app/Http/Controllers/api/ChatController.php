<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{

    public function createConversation(Request $request)
    {
        try {
            $conversation = Conversation::create([
                'name' => $request->name,
                'type' => $request->type
            ]);

            return response()->json([
                'conversation' => $conversation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function AllConversation()
    {
        try {
            $allConversation = Conversation::whereHas('membres',function($query){
                $query->where('user_id',Auth::id());
            })->with('latestMessage')->get();
            return response()->json([
                'conversation' => $allConversation
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function allDiscussion(Conversation $conversation)
    {
        try {
            $discussion = Message::with('user')->where('conversation_id',$conversation->id)->get();
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

            $message = Auth::user()->Messages()->create([
                'conversation_id' => $request->conversation_id,
                'message' => $request->messages
            ]);
            return response()->json([
                'conversation' => $message
            ],201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
