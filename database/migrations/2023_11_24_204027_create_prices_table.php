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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id')->comment('کد بخش');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->double('entrance_price')->default(0)->comment('نرخ ورودی');
            $table->integer('from')->comment('از دقیقه');
            $table->integer('to')->comment('تا دقیقه');
            $table->string('calc_type')->comment('شیوه محاسبه');
            $table->double('price')->comment('نرخ');
            $table->string('price_type')->comment('نوع نرخ');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
