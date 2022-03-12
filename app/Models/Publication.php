<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = [
        'countCommentaire',
        'countReaction',
    ];

    public function getCountCommentaireAttribute()
    {
        return $this->commentaires()->count();;
    }

    public function getCountReactionAttribute()
    {
        return $this->reactable()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
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

    public function publicable()
    {
        return $this->morphTo();
    }

    public function sharable()
    {
        return $this->morphOne(Shares::class,'sharable');
    }

    public function reactable()
    {
        return $this->morphMany(ReactAction::class,'reactable');
    }
}
