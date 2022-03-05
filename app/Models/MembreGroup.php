<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembreGroup extends Model
{
    use HasFactory;
    protected $guarded = [];
    // public function group()
    // {
    //    return $this->belongsTo(Group::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function role()
    {
        return $this->belongsTo(RoleUser::class,'role_id');
    }
}
