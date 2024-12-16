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
        Schema::create('factors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('game_id')->nullable();
            $table->foreign('game_id')->references('id')->on('games');
            $table->unsignedBigInteger('person_id');
            $table->foreign('person_id')->references('id')->on('people');
            $table->string('person_fullname');
            $table->string('person_mobile');
            $table->double('total_price')->default(0)->comment('قیمت قبل از تخفیف');
            $table->unsignedBigInteger('offer_code')->nullable();
            $table->double('offer_price')->default(0);
            $table->double('final_price')->default(0)->comment('قیمت بعد از تخفیف');
            $table->tinyInteger('closed')->unsigned()->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factors');
    }
};
