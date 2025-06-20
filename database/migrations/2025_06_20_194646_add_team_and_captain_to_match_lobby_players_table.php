<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('match_lobby_players', function (Blueprint $table) {
        $table->string('team')->nullable(); // 'ct' vagy 't'
        $table->boolean('is_captain')->default(false);
    });
}

public function down(): void
{
    Schema::table('match_lobby_players', function (Blueprint $table) {
        $table->dropColumn(['team', 'is_captain']);
    });
}
};
