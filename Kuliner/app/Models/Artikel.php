<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'poto',
        'nama_kuliner',
        'daerah',
        'deskripsi',
    ];

    public function users(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likes(){
        return $this->hasMany(Like::class, 'artikel_id', 'id');
    }
    
}
