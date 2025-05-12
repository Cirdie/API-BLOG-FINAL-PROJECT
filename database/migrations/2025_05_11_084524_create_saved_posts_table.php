<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedPostsTable extends Migration
{
    public function up(): void
    {
        Schema::create('saved_posts', function (Blueprint $table) {
            $table->bigIncrements('Saved_Id');
            $table->unsignedBigInteger('User_Id');
            $table->unsignedBigInteger('Post_Id');
            $table->timestamps();

            $table->foreign('User_Id')->references('User_Id')->on('users')->onDelete('cascade');
            $table->foreign('Post_Id')->references('Post_Id')->on('posts')->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('saved_posts');
    }
}
