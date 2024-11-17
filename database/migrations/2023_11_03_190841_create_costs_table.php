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
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->comment('کاربر ایجاد');
            $table->double('price')->comment('مبلغ');
            $table->text('details')->nullable()->comment('توضیحات');
            $table->timestamp('date')->comment('تاریخ اعمال');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};
