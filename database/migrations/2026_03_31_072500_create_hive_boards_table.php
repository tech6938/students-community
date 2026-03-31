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
        Schema::create('hive_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('title');
            $table->string('place')->nullable();
            $table->json('tags')->nullable(); // store multiple tags as JSON array
            $table->text('desc')->nullable();
            $table->string('post_as')->nullable();
            $table->string('file')->nullable(); // can store img/pdf path
            $table->boolean('link_to_journal')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hive_boards');
    }
};