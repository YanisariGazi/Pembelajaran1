<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'no_hp',
        'pekerjaan',
        'alamat_lengkap',
        'gambar'
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


    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            return $this->role && in_array($this->role, $roles);
        }

        return $this->role && $this->role == $roles;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function komentars()
    {
        return $this->hasMany(Komentar::class);
    }

    public function profilUser()
    {
        return $this->hasOne(ProfilUser::class, 'user_id');
    }

    public function profil()
    {
        return $this->hasOne(ProfilUser::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id')
            ->withPivot('follower_name')
            ->with('profil');
    }

    public function getFollowerCount()
    {
        return $this->followers()->count();
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role == $roles;
    }


}

