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
        Schema::create('food_reservs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fp_id');
            $table->foreign('fp_id')->references('id')->on('food_plans');
            $table->unsignedBigInteger('p_id');
            $table->foreign('p_id')->references('id')->on('people');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_reservs');
    }
};
