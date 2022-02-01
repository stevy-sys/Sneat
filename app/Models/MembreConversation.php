<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembreConversation extends Model
{
    use HasFactory;

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
