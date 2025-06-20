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
        Schema::table('match_lobbies', function (Blueprint $table) {
            $table->foreignId('game_server_id')->nullable()->after('started_at')->constrained('game_servers')->onDelete('set null');
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
