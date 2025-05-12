<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('Post_Id'); // Custom PK
            $table->unsignedBigInteger('User_Id'); // FK to users.user_id
            $table->string('title');
            $table->text('content');
            $table->boolean('is_approved')->default(false); // Admin approval flag
            $table->timestamps();

            // Foreign key constraint using correct PK from users table
            $table->foreign('User_Id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
}
