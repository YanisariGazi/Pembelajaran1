<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomentarsTable extends Migration
{
    public function up()
    {
        Schema::create('komentars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->text('konten');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('balasan_komentars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komentar_id');
            $table->unsignedBigInteger('user_id');
            $table->text('konten');
            $table->timestamps();

            $table->foreign('komentar_id')->references('id')->on('komentars')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('balasan_komentars');
        Schema::dropIfExists('komentars');
    }
}
