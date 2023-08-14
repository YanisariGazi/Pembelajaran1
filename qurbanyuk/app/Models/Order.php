<?php

namespace App\Models;

use App\Models\Produk;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    public function users(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function produks()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
