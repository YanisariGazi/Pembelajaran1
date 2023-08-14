<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users');
            $table->foreignId('id_produk')->constrained('produks');
            $table->string('nama');
            $table->string('alamat');
            $table->string('no_tlp');
            $table->string('status')->default('Di Proses');
            $table->string('image')->nullable();
            $table->string('status_pembayaran')->default('Belum Lunas');
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
