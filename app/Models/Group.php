<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function membresGroupe()
    {
        return $this->hasMany(MembreGroup::class,'group_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function invitable()
    {
        return $this->morphMany(Invitation::class,'invitable');
    }
    public function sharable()
    {
        return $this->morphOne(Shares::class,'sharable');
    }

    public function publicable()
    {
        return $this->morphMany(Publication::class,'publicable');
    }
}
