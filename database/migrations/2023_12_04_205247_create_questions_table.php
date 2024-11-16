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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vote_id')->comment('کد نظرسنجی');
            $table->foreign('vote_id')->references('id')->on('votes');
            $table->string('title')->comment('عنوان');
            $table->string('type')->comment('نوع');
            $table->integer('display_order')->comment('ترتیب نمایش');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
