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
      Schema::create('profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('name');
    $table->string('username')->unique();

    $table->string('home_school')->nullable();
    $table->string('abroad_school')->nullable();
    $table->string('home_city')->nullable();
    $table->string('current_city')->nullable();

    $table->json('languages')->nullable();
    $table->json('interests')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
