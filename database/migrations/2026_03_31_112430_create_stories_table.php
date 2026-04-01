<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();

            $table->integer('post_type');
            $table->string('title');
            $table->text('desc');
            $table->string('place')->nullable();

            // Store tags as JSON
            $table->json('tags')->nullable();

            $table->string('img')->nullable();

            $table->string('post_as');

            // boolean (0 or 1)
            $table->boolean('link_to_journal')->default(false);

            $table->unsignedBigInteger('user_id');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
