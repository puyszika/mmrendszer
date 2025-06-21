<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('match_lobbies', function (Blueprint $table) {
            $table->string('map')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('match_lobbies', function (Blueprint $table) {
            $table->dropColumn('map');
        });
    }

};
