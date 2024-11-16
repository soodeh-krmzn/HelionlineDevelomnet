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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('نام کد');
            $table->string('type')->comment('(درصد یا مبلغ) نوع کد');
            $table->integer('per')->length(11)->comment('مقدار');
            $table->double('min_price')->comment('حداقل مبلغ');
            $table->string('calc')->default('all')->comment('نحوه محاسبه تخفیف');
            $table->text('details')->comment('توضیحات')->nullable();
            $table->integer('times_used')->default(0)->comment('تعداد دفعات استفاده');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
