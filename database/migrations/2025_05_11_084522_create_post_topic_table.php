<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTopicTable extends Migration
{
    public function up(): void
    {
        Schema::create('post_topic', function (Blueprint $table) {
            $table->bigIncrements('Posts_Id');
            $table->unsignedBigInteger('Post_Id');
            $table->unsignedBigInteger('Topic_Id');
            $table->timestamps();

            $table->foreign('Post_Id')->references('Post_Id')->on('posts')->onDelete('cascade');
            $table->foreign('Topic_Id')->references('Topic_Id')->on('topics')->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('post_topic');
    }
}
