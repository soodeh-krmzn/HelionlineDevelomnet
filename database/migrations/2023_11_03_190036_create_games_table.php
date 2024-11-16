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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('کد کاربر');
            $table->unsignedBigInteger('person_id')->comment('کد شخص');
            $table->foreign('person_id')->references('id')->on('people');
            $table->string('person_fullname')->comment('نام کامل');
            $table->string('person_mobile')->comment('شماره موبایل');
            $table->integer('type')->length(11)->nullable();
            $table->integer('count')->length(11)->default(1)->comment('تعداد');
            $table->string('in_time')->nullable()->comment('ساعت ورود');
            $table->string('out_time')->nullable()->comment('ساعت خروج');
            $table->date('in_date')->nullable()->comment('تاریخ ورود');
            $table->date('out_date')->nullable()->comment('تاریخ خروج');
            $table->smallInteger('total')->default(0);
            $table->smallInteger('total_vip')->default(0);
            $table->smallInteger('extra')->default(0);
            $table->double('total_price')->nullable()->defaul(0)->comment('مبلغ عادی');
            $table->double('total_vip_price')->default(0)->comment('مبلغ ویژه');
            $table->double('extra_price')->default(0)->comment('مبلغ مازاد');
            $table->smallInteger('used_sharj')->default(0);

            $table->integer('initial_sharj')->default(0);
            $table->string('sharj_package')->nullable();
            $table->string('sharj_type')->nullable();
            $table->double('final_price_before_round')->nullable();
            $table->double('final_price_after_round')->nullable();

            $table->double('login_price')->default(0);
            $table->double('game_price')->default(0)->comment('مبلغ بازی');
            $table->double('total_shop')->default(0);
            $table->smallInteger('offer_code')->default(0);
            $table->double('offer_price')->default(0);
            $table->string('offer_name')->nullable();
            $table->string('offer_calc')->default('all')->comment('نحوه محاسبه تخفیف');
            $table->double('final_price')->default(0)->comment('مبلغ بعد از تخفیف');
            $table->integer('status')->length(11)->default(0)->comment('وضعیت حضور');
            $table->text('adjective')->nullable()->comment('امانتی');
            $table->string('enter_type')->default('automatic');
            $table->unsignedBigInteger('section_id')->comment('کد بخش');
            $table->string('section_name')->nullable()->comment('نام بخش');
            $table->unsignedBigInteger('station_id')->nullable()->comment('کد ایستگاه');
            $table->string('station_name')->nullable()->comment('نام ایستگاه');
            $table->unsignedBigInteger('counter_id')->nullable()->comment('کد شمارنده');
            $table->string('counter_name')->nullable()->comment('نام شمارنده');
            $table->integer('counter_min')->nullable()->comment('مقدار شمارنده');
            $table->integer('counter_passed')->nullable()->comment('مقدار سپری شده');
            $table->double('deposit')->default(0)->comment('پیش پرداخت');
            $table->string('deposit_type')->nullable()->comment('نوع پیش پرداخت');
            $table->string('accompany_name')->nullable()->comment('نام همراه');
            $table->string('accompany_mobile')->nullable()->comment('موبایل همراه');
            $table->string('accompany_relation')->nullable()->comment('نسبت همراه');
            $table->integer('group_id')->nullable()->comment('کد گروه');
            $table->tinyInteger('seperated')->default(0);
            $table->double('vat_rate')->nullable();
            $table->double('vat_price')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
