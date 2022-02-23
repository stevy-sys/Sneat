<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function membres()
    {
        return $this->hasMany(MembreConversation::class,'conversation_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class,'conversation_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class,'conversation_id')->latest()->with('user');
    }

    public function whoDiscuss()
    {
        $membre = $this->hasOne(MembreConversation::class,'conversation_id') ;
        return $membre->with('user')->where('user_id','<>',Auth::id());
    }
}
