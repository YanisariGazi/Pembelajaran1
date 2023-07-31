<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'artikel_id',
        'like'
    ];

    public function artikels(){
        return $this->belongsTo(Artikel::class, 'artikel_id', 'id');
    }

    public function users(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
