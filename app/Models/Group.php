<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public function membresGroupe()
    {
        return $this->hasMany(MembreGroup::class,'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }
}
