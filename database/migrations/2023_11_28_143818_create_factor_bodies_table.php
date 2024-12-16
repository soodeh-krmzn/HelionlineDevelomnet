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
        Schema::create('factor_bodies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('factor_id');
            $table->foreign('factor_id')->references('id')->on('factors');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->string('product_name');
            $table->double('product_price')->comment('قیمت واحد');
            $table->double('product_buy_price')->comment('قیمت خرید واحد');
            $table->integer('count')->default(0)->comment('تعداد');
            $table->double('body_price')->default(0)->comment('قمیت کل');
            $table->double('body_buy_price')->default(0)->comment('قمیت خرید کل');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factor_bodies');
    }
};
