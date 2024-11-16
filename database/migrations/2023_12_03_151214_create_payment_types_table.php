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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('نام');
            $table->string('label')->comment('برچسب');
            $table->string('details')->nullable()->comment('توضیحات');
            $table->boolean('status')->comment('وضعیت');
            $table->boolean('club')->default(1)->comment('مشمول امتیاز باشگاه');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
