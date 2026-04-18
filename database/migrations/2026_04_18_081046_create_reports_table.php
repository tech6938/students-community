<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['community', 'hiveboard', 'story']);

            $table->unsignedBigInteger('community_id')->nullable();
            $table->unsignedBigInteger('hiveboards_id')->nullable();
            $table->unsignedBigInteger('stories_id')->nullable();

            $table->string('issue');
            $table->string('description');

            $table->timestamps();

            // Optional indexes (recommended)
            $table->index(['type']);
            $table->index(['community_id']);
            $table->index(['hiveboards_id']);
            $table->index(['stories_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
