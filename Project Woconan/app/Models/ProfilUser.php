<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'gambar',
        'status',
        'hobi',
        'kewarganegaraan',
        'jenis_kelamin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function follower()
    {
        return $this->hasOne(Follower::class, 'profil_user_id');
    }


}
