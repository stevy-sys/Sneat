<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function inviteur()
    {
        return $this->belongsTo(User::class,'inviteur');
    }

    public function invitable()
    {
        return $this->morphTo();
    }

    public function invite()
    {
        return $this->belongsTo(User::class,'invite');
    }
}
