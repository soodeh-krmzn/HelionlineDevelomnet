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
        Schema::create('my_payments', function (Blueprint $table) {
            $table->id();
            $table->string('authority')->comment('شناسه پرداخت');
            $table->string('status')->nullable()->comment('وضعیت');
            $table->string('ref_id')->nullable()->comment('رسید دیجیتال');
            $table->string('message')->nullable()->comment('متن پیام');
            $table->double('price')->comment('مبلغ');
            $table->string('type')->comment('نوع پرداخت');
            $table->string('username')->comment('نام کاربری');
            $table->string('card')->nullable()->comment('شماره کارت');
            $table->dateTime('pay_created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_payments');
    }
};
