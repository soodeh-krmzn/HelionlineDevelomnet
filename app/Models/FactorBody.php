<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FactorBody extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['factor_id', 'product_id', 'product_name', 'product_price', 'product_buy_price', 'count', 'body_price', 'body_buy_price'];

    public function factor()
    {
        return $this->belongsTo(Factor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function showIndex($f_id)
    {
        $factor = Factor::find($f_id);
        $bodies = $factor->bodies;
        if($bodies->count() > 0) {
            ?>
            <table class="table table-hover table-bordered border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th><?=__('نام محصول')?></th>
                        <th><?=__('قیمت واحد')?></th>
                        <th><?=__('تعداد')?></th>
                        <th><?=__('کل')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach($bodies as $body) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $body->product_name; ?></td>
                            <td><?php echo cnf($body->product_price); ?></td>
                            <td><?php echo $body->count; ?></td>
                            <td><?php echo cnf($body->body_price);?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                    <tr>
                        <td class="text-center" colspan="6"><h4><?=__('مجموع')?>: <?php echo cnf($factor->total_price) ?></h4></td>
                    </tr>
                </tbody>
            </table>
            <?php
        }
    }
}
