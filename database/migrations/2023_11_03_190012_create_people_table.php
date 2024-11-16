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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->length(11)->comment('کاربر ایجاد');
            $table->string('name')->nullable()->comment('نام');
            $table->string('family')->nullable()->comment('نام خانوادگی');
            $table->string('fname')->nullable()->comment('نام پدر');
            $table->date('birth')->nullable()->comment('تاریخ تولد میلادی');
            $table->string('shamsi_birth')->nullable()->comment('تاریخ تولد شمسی');
            $table->text('address')->nullable()->comment('آدرس');
            $table->string('card_code')->nullable()->comment('کد کارت');
            $table->string('reg_code')->nullable()->comment('کد اشتراک');
            $table->boolean('gender')->comment('جنسیت');
            $table->string('national_code')->nullable()->comment('کد ملی');
            $table->string('mobile')->nullable()->comment('موبایل');
            $table->integer('sharj')->length(11)->nullable()->default(0)->comment('شارژ بسته اشتراک');
            $table->string('sharj_type')->nullable();
            $table->string('expire')->nullable()->comment('تاریخ انقضا بسته اشتراک');
            $table->integer('pack')->length(11)->nullable()->comment('کد بسته اشتراک');
            $table->date('commitment')->nullable()->comment('تاریخ تکمیل تعهدنامه');
            $table->text('profile')->nullable()->comment('تصویر پروفایل');
            $table->boolean('club')->default(0)->comment('باشگاه مشتریان');
            $table->double('rate')->nullable()->comment('امتیاز');
            $table->double('wallet_value')->default(0)->comment('شارژ کیف پول');
            $table->double('balance')->default(0)->comment('تراز مالی');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
