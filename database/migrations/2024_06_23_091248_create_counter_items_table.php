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
        Schema::create('counter_items', function (Blueprint $table) {
            $table->id();
            $table->integer('g_id')->default(0);
            $table->string('title');
            $table->dateTime('start_date');
            $table->integer('min_duration');
            $table->dateTime('stop_date')->nullable();
            $table->integer('stop_min_duration')->default(0);
            $table->tinyInteger('end')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_items');
    }
};
