<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('match_lobbies', function (Blueprint $table) {
            $table->foreignId('captain_ct_id')->nullable()->after('code')->constrained('users')->onDelete('cascade');
            $table->foreignId('captain_t_id')->nullable()->after('captain_ct_id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('match_lobbies', function (Blueprint $table) {
            $table->dropColumn(['captain_ct_id', 'captain_t_id']);
        });
    }
};
