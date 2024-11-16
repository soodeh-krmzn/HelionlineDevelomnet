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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id')->comment('کد کاربر');
            $table->foreign('person_id')->references('id')->on('people');
            $table->double('balance')->comment('موجودی / مانده حساب');
            $table->double('price')->comment('مبلغ');
            $table->integer('gift_percent')->comment('درصد هدیه')->default(0);
            $table->double('final_price')->comment('مبلغ نهایی');
            $table->string('description')->nullable()->comment('توضیحات');
            $table->date('expire')->nullable()->comment('تاریخ انقضاء');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
