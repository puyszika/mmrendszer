<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('map_bans', function (Blueprint $table) {
            $table->enum('action', ['banned', 'picked'])->default('banned')->after('map');
        });
    }

    public function down(): void
    {
        Schema::table('map_bans', function (Blueprint $table) {
            $table->dropColumn('action');
        });
    }

};
