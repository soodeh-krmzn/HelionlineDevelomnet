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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->double('price')->comment('مبلغ برای یک امتیاز');
            $table->double('rate')->comment('شارژ به ازای یک امتیاز');
            $table->double('min_price')->default(0)->comment('حداقل پرداخت');
            $table->string('type')->comment('نوع اعمال امتیاز');
            $table->string('expire')->nullable()->comment('انقضای امتیاز (روز)');
            $table->boolean('all_people')->default(1)->comment('محاسبه برای همه اشخاص');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
