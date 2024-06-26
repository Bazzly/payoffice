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
        Schema::create('pings_monitoring', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('apiurl');
            $table->string('serverStatus');
            $table->string('serverPing');
            $table->string('userPing');
            $table->boolean('is_default')->nullable()->default(0);
            $table->timestamps();
  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pings_monitoring');
    }
};