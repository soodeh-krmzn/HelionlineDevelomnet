<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sections')->insert([
            'name' => __('خانه بازی'),
            'show_status' => 1,
        ]);

        DB::table('categories')->insert([
            'name' => __('خوراکی'),
        ]);

        DB::table('products')->insert([[
            'name' => __('کیک'),
            'stock' => 10,
            'buy' => 5000,
            'sale' => 8000,
        ], [
            'name' => __('بستنی'),
            'stock' => 10,
            'buy' => 8000,
            'sale' => 10000,
        ], [
            'name' => __('کلوچه'),
            'stock' => 10,
            'buy' => 10000,
            'sale' => 15000,
        ]]);

        DB::table('product_category')->insert([[
            'product_id' => 1,
            'category_id' => 1,
        ], [
            'product_id' => 2,
            'category_id' => 1,
        ], [
            'product_id' => 3,
            'category_id' => 1,
        ]]);
        DB::table('prices')->insert([
            'section_id' => 1,
            'entrance_price' => 5000,
            'from' => 1,
            'to' => 500,
            'calc_type' => 'min',
            'price' => 2000,
            'price_type' => 'normal',
        ]);
        DB::table('payment_types')->insert([
            [
                'name' => __('کارتخوان'),
                'label' => __("کارتخوان"),
                'status' => 1,
            ], [
                'name' => __('نقدی'),
                'label' => __("نقدی"),
                'status' => 1,
            ], [
                'name' => __('کارت_به_کارت'),
                'label' => __("کارت به کارت"),
                'status' => 1,
            ]
        ]);
        DB::table('settings')->insert([
            'meta_key' => 'default_payment_type',
            'meta_value' => __('کارتخوان')
        ]);

        DB::table('settings')->insert([
            'meta_key' => 'offline_mode',
            'meta_value' => __('0')
        ]);
    }
}
