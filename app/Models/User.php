<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

    protected $appends = ['activeProfil', 'isFriend'/*'amiCommun'*/];

    public function Messages()
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function profil()
    {
        return $this->hasOne(Profil::class, 'user_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'user_id');
    }

    public function groupMember()
    {
        return $this->hasMany(MembreGroup::class, 'user_id');
    }

    public function MyGroupe()
    {
        return  MembreGroup::where('user_id', $this->id);
    }


    public function myInvitation()
    {
        $this->hasMany(Invitation::class, 'inviteur');
    }

    public function whoInvitMe()
    {
        $this->hasMany(Invitation::class, 'invite');
    }

    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'invitable');
    }

    public function membreConversations()
    {
        return $this->hasMany(MembreConversation::class, 'user_id');
    }

    public function membreGroup()
    {
        return $this->belongsTo(MembreGroup::class);
    }

    public function publications()
    {
        return $this->hasMany(Publication::class, 'user_id');
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends_pivot', 'user_id', 'friend_id')->withTimestamps();
    }

    public function publicationStatus()
    {
        return $this->morphMany(Publication::class, 'publicable');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
    public function sharable()
    {
        return $this->morphOne(Shares::class, 'sharable');
    }

    public function getActiveProfilAttribute()
    {
        return $this->media()->where('active', true)->first();
    }

    public function getIsFriendAttribute()
    {
        if ($this->id != Auth::id()) {
            $exist = $this->friends()->where('friends_pivot.friend_id',Auth::id())->first();
            if (isset($exist)) {
                return true ;
            }
        }
        return false;
    }

    public function amisCommuns()
    {
        return $this->belongsToMany(User::class, 'friends_pivot', 'user_id', 'friend_id')->withTimestamps();
    }

    public function scopeAmisCommun(Builder $query, $authUser)
    {
        return $query
        ->whereHas('amisCommuns', function ($query) use ($authUser) {
            $query->whereIn('friends_pivot.friend_id', $authUser->friends->pluck('id'));
        })
        ->with(['amisCommuns' => function ($query) use ($authUser) {
            $query->whereIn('friends_pivot.friend_id', $authUser->friends->pluck('id'));
        }]); // Limite les résultats à 5 utilisateurs;
    }

    public function scopeRandomCommunUser(Builder $query, $authUser)
    {
        return $query->with(['amisCommuns' => function ($query) use ($authUser) {
            $query->whereIn('friends_pivot.friend_id', $authUser->friends->pluck('id'));
        }]); // Limite les résultats à 5 utilisateurs;
    }
}
