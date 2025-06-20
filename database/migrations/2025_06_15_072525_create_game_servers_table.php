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
        Schema::create('game_servers', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // pl. cs2_server_1
        $table->integer('port');
        $table->string('token');
        $table->enum('status', ['available', 'busy'])->default('available');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_servers');
    }
};
