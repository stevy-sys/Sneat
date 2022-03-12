<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReactAction extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(user::class,'user_id');
    }

    public function reactionType()
    {
        return $this->belongsTo(Reaction::class,'reaction_id');
    }

    public function reactable()
    {
        return $this->morphTo();
    }
}
