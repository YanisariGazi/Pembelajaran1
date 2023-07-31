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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('poto');
            $table->string('nama_kuliner');
            $table->string('daerah');
            $table->string('deskripsi');
            $table->integer('likes_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->integer('report')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
