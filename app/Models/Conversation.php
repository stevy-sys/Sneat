<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasOne(Message::class,'conversation_id')->latest();
    }
}
