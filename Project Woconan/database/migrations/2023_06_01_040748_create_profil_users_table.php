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
        Schema::create('profil_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('gambar')->nullable();
            $table->string('status')->nullable(); // Kolom boleh dikosongkan
            $table->string('hobi')->nullable(); // Kolom boleh dikosongkan
            $table->string('kewarganegaraan')->nullable(); // Kolom boleh dikosongkan
            $table->string('jenis_kelamin')->nullable(); // Kolom boleh dikosongkan
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_users');
    }
};
