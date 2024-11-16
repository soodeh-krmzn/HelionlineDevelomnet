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
        Schema::create('cost_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('نام');
            $table->unsignedBigInteger('parent_id')->comment('دسته والد')->nullable();
            $table->text('details')->comment('توضیحات')->nullable();
            $table->text('display_order')->comment('ترتیب نمایش')->nullable();
            $table->string('code')->comment('کد دسته')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_categories');
    }
};
