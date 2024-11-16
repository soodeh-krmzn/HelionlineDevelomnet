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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('نام کالا');
            $table->integer('stock')->length(11)->comment('موجودی');
            $table->double('buy')->comment('قیمت خرید');
            $table->double('sale')->comment('قیمت فروش');
            $table->integer('cart')->length(11)->nullable();
            $table->string('image')->nullable()->comment('عکس محصول');
            $table->boolean('status')->default(1)->comment('وضعیت نمایش محصول');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
