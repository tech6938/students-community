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
    Schema::create('communities', function (Blueprint $table) {
        $table->id();
        $table->string('img')->nullable(); // image path or URL
        $table->string('place');
        $table->text('caption')->nullable();
        $table->string('post_as'); // e.g. user/admin
        $table->boolean('link_to_journal')->default(false);
        $table->foreignId('user_id');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
