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
        Schema::create('game_metas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('g_id')->comment('کد بازی');
            $table->foreign('g_id')->references('id')->on('games');
            $table->string('key')->comment('کلید متای بازی');
            $table->string('value')->comment('مقدار متای بازی');
            $table->dateTime('start')->nullable()->comment('زمان شروع');
            $table->dateTime('end')->nullable()->comment('زمان اتمام');

            $table->double('rate_price')->default(0)->comment('نرخ ردیف برای لاگ');
            $table->string('rate_type')->nullable()->comment('نوع نرخ');

            $table->tinyInteger('close')->default(0);
            $table->integer('u_id')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_metas');
    }
};
