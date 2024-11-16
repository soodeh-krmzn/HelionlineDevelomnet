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
        Schema::create('buy_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p_id')->comment('کد شخص');
            $table->foreign('p_id')->references('id')->on('people');
            $table->unsignedBigInteger('pk_id')->comment('کد بسته');
            $table->foreign('pk_id')->references('id')->on('packages');
            $table->float('time')->comment('مدت زمان');
            $table->string('expire')->comment('تاریخ انقضا');
            $table->string('type');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_packages');
    }
};
