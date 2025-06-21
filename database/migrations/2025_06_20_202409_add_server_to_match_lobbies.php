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
        Schema::table('match_lobbies', function (Blueprint $table) {
            $table->foreignId('server_id')->nullable()->constrained('game_servers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_lobbies', function (Blueprint $table) {
            //
        });
    }
};
