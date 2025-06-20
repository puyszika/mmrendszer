<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('match_lobbies', function (Blueprint $table) {
            $table->id();
            $table->uuid('code')->unique();
            $table->string('status')->default('pending'); // pending, accepted, canceled
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_lobbies');
    }
};
