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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // pl. inst-lan01
            $table->string('ip')->default('server.versuscs.hu');
            $table->integer('port');
            $table->string('status')->default('available'); // available / in_use
            $table->string('path')->default('/mnt/cs2ssd/cs2-multiserver');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
