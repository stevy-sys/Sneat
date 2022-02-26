<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class,'mediable');
    }

    public function commentaires()
    {
        return $this->morphMany(Commentaire::class,'commentable');
    }

    public function actualites()
    {
        return $this->morphMany(Actualites::class,'actualable');
    }
}
