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
            $table->json('map_pool')->nullable();
            $table->string('selected_map')->nullable();
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
