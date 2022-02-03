<?php

namespace App\Models;

use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function Messages()
    {
        return $this->hasMany(Message::class,'user_id');
    }

    public function profil()
    {
        return $this->hasOne(Profil::class,'user_id');
    }

    public function groups()
    {
        return $this->hasMany(MembreGroup::class,'user_id');
    }

    public function myInvitation()
    {
        $this->hasMany(Invitation::class,'inviteur');
    }

    public function whoInvitMe()
    {
        $this->hasMany(Invitation::class,'invite');
    }

    public function invitation()
    {
       return $this->morphMany(Invitation::class,'invitable');
    }

    public function membreConversation()
    {
        return $this->hasMany(MembreConversation::class,'user_id');
    }
}
