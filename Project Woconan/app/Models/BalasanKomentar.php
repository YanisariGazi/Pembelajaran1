<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalasanKomentar extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'komentar_id', 'konten'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function komentar()
    {
        return $this->belongsTo(Komentar::class);
    }
}
