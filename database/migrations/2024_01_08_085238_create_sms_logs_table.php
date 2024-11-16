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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sms_id')->nullable()->comment('کد یکتای پیامک');
            $table->integer('parts')->nullable()->comment('تعداد صفحه');
            $table->string('tariff')->nullable()->comment('تعرفه');
            $table->string('recipient')->nullable()->comment('گیرندگان');
            $table->text('message')->comment('متن');
            $table->string('category_name')->comment('دسته پیامک');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
