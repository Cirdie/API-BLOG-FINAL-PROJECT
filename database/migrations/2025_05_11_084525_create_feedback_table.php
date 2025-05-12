<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->bigIncrements('Feedback_Id');
            $table->unsignedBigInteger('User_Id')->nullable();
            $table->text('message');
            $table->timestamps();

            $table->foreign('User_Id')->references('User_Id')->on('users')->onDelete('set null');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
}
