<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buzzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('location');
            $table->string('place');
            $table->string('buzz_type');
            $table->json('tags')->nullable();
            $table->boolean('beelo_mission')->default(0);
            $table->float('rating', 3, 1); // e.g. 2.5, 4.5
            $table->string('img')->nullable();
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buzzes');
    }
};
