<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;

class Price extends Main
{
    use HasFactory;

    protected $fillable = ['section_id','entrance_price', 'from', 'to', 'calc_type', 'price', 'price_type'];

    public function priceTable($sectionId)
    {
        $prices = Price::where('section_id', $sectionId)->orderBy('price_type')->get();
        if ($prices->count() > 0) {
            ?>
            <table class="table table-hover">
                <thead>
                    <tr class="table-primary">
                        <th style="min-height: 150px;"><?=__('از دقیقه')?></th>
                        <th style="min-height: 150px;"><?=__('تا دقیقه')?></th>
                        <th style="min-height: 150px;"><?=__('مبلغ ورودی')?></th>
                        <th style="min-height: 150px;"><?=__('نوع محاسبه')?></th>
                        <th style="min-height: 150px;"><?=__('نرخ')?></th>
                        <th style="min-height: 150px;"><?=__('نوع نرخ')?></th>
                        <th style="min-height: 150px;"><?=__('عملیات')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($prices as $price) { ?>
                        <tr>
                            <td>
                                <input type="text" data-id="from<?php echo $price->id; ?>" class="form-control just-numbers money-filter" placeholder="<?=__('از دقیقه')?>..." value="<?php echo $price->from; ?>">
                                <input type="hidden" id="from<?php echo $price->id; ?>" class="form-control just-numbers" value="<?php echo $price->from; ?>">
                            </td>
                            <td>
                                <input type="text" data-id="to<?php echo $price->id; ?>" class="form-control just-numbers money-filter" placeholder="<?=__('تا دقیقه')?>..." value="<?php echo $price->to; ?>">
                                <input type="hidden" id="to<?php echo $price->id; ?>" class="form-control just-numbers" value="<?php echo $price->to; ?>">
                            </td>
                            <td>
                                <input type="text" data-id="entrance-price<?php echo $price->id ?>" class="form-control money-filter" placeholder="<?=__('مبلغ ورودی')?>..." value="<?php echo ($price->entrance_price) ?>">
                                <input type="hidden" id="entrance-price<?php echo $price->id ?>" class="form-control just-numbers" value="<?php echo $price->entrance_price ?>">
                            </td>
                            <td>
                                <select id="calc-type<?php echo $price->id; ?>" class="form-select ">
                                    <option <?php echo ($price->calc_type == "min") ? "selected" : ""; ?> value="min"><?=__('دقیقه ای')?></option>
                                    <option <?php echo ($price->calc_type == "all") ? "selected" : ""; ?> value="all"><?=__('کلی')?></option>
                                </select>
                            </td>
                            <td>
                                <input type="text" data-id="price<?php echo $price->id; ?>" class="form-control just-numbers money-filter" placeholder="<?=__('نرخ')?>..." value="<?php echo cnf($price->price); ?>">
                                <input type="hidden" id="price<?php echo $price->id; ?>" class="form-control just-numbers " value="<?php echo $price->price; ?>">
                            </td>
                            <td>
                                <select id="price-type<?php echo $price->id; ?>" class="form-select ">
                                    <option <?php echo ($price->price_type == "normal") ? "selected" : ""; ?> value="normal"><?=__('عادی')?></option>
                                    <option <?php echo ($price->price_type == "vip") ? "selected" : ""; ?> value="vip"><?=__('ویژه')?></option>
                                    <option <?php echo ($price->price_type == "extra") ? "selected" : ""; ?> value="extra"><?=__('مازاد')?></option>
                                </select>
                            </td>
                            <td>
                                <?php if (Gate::allows('update')) { ?>
                                    <button class="btn btn-warning btn-sm update-price" data-id="<?php echo $price->id; ?>"><i class="bx bx-edit"></i></button>
                                <?php }
                                if (Gate::allows('delete')) { ?>
                                    <button class="btn btn-danger btn-sm delete-price" data-id="<?php echo $price->id; ?>"><i class="bx bx-trash"></i></button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        } else {
            ?>
            <div class="alert alert-danger text-center"><?=__('هنوز هیچ تعرفه ای برای این بخش تعریف نشده است.')?></div>
            <?php
        }
    }

}
