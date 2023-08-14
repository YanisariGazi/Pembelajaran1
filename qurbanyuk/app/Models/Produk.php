<?php

namespace App\Models;

use App\Models\Order;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $guarded = ['id'];

    public function order()
    {
        return $this->hasMany(Order::class, 'id_produk');
    }

    public function users()
    {
        return $this->belongsTo(Produk::class);
    }
}
