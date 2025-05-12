<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->bigIncrements('Topic_Id');
            $table->string('name');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
}
