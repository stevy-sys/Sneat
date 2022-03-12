<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shares extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sharable()
    {
        return $this->morphTo();
    }

    public function actualites()
    {
        return $this->morphMany(Actualites::class,'actualable');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function publicable()
    {
        return $this->morphOne(Publication::class,'publicable');
    }
}
