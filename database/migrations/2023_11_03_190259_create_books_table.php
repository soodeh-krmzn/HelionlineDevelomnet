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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('نام کتاب');
            $table->string('cage')->nullable()->comment('نام قفسه');
            $table->string('cage_location')->nullable()->comment('محل قفسه');
            $table->string('author')->nullable()->comment('نام نویسنده');
            $table->string('publisher')->nullable()->comment('نام ناشر');
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
        Schema::dropIfExists('books');
    }
};
