<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    use HasFactory;

    protected $table = 'komentars';

    protected $fillable = ['user_id', 'post_id', 'konten'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balasan()
    {
        return $this->hasMany(BalasanKomentar::class, 'komentar_id');
    }

    public function replies()
    {
        return $this->hasMany(Komentar::class, 'parent_id');
    }

}
