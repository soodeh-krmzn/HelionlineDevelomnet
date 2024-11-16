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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('نام کلاس');
            $table->unsignedBigInteger('user_id')->comment('مربی');
            $table->date('start')->nullable()->comment('تاریخ شروع');
            $table->integer('sessions')->nullable()->comment('تعداد جلسات');
            $table->integer('capacity')->nullable()->comment('ظرفیت');
            $table->double('price')->comment('هزینه');
            $table->text('details')->nullable()->comment('توضیحات');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
