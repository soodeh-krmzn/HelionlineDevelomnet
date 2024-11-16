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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('نام');
            $table->double('price')->comment('هزینه خرید');
            $table->integer('expire_time')->length(11)->comment('اعتبار به دقیقه/مرتبه');
            $table->integer('expire_day')->length(11)->comment('اعتبار به روز');
            $table->string('type')->default('min')->comment('نوع بسته');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
