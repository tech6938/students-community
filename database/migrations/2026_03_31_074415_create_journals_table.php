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
       Schema::create('journals', function (Blueprint $table) {
    $table->id();
    $table->integer('category');
    $table->string('title');
    $table->string('place');
    $table->float('rating');
    $table->text('notes');
    $table->string('img')->nullable();
    $table->boolean('link_to_buzz')->default(0);
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
