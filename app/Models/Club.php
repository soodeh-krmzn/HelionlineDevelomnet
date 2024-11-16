<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['price', 'rate', 'min_price', 'type', 'expire', 'all_people'];

    public function crud($type)
    {
        if ($type == "") {
            ?>
            <div class="row m-0">
                <div class="col-md-12 alert alert-danger text-center"><?=__('لطفا یکی از گزینه ها را انتخاب کنید.')?></div>
            </div>
            <?php
        } else {
            $club = Club::where('type', $type)->first();
            if ($club) {
                $price = $club->price;
                $rate = $club->rate;
                $min_price = $club->min_price;
                $expire = $club->expire;
                $people = $club->all_people;
            } else {
                $price = "";
                $rate = "";
                $min_price = "";
                $expire = "";
                $people = "";
            }
            ?>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('مبلغ پرداختی جهت دریافت یک امتیاز')?> <span class="text-danger">*</span></label>
                    <input type="text" name="price" data-id="price" class="form-control money-filter" placeholder="<?=__('مبلغ')?>..." value="<?php echo ($price != "" ? cnf($price) : ""); ?>">
                    <input type="hidden" name="price" id="price" class="form-control" value="<?php echo $price; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('مبلغ شارژ کیف پول به ازای هر امتیاز')?> <span class="text-danger">*</span></label>
                    <input type="text" name="rate" data-id="rate" class="form-control money-filter" placeholder="<?=__('شارژ')?>..." value="<?php echo ($rate != "" ? cnf($rate) : ""); ?>">
                    <input type="hidden" name="rate" id="rate" class="form-control" value="<?php echo $rate; ?>">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('حداقل مبلغ')?></label>
                    <input type="text" name="min_price" data-id="min-price" class="form-control money-filter" placeholder="<?=__('حداقل مبلغ')?>..." value="<?php echo ($min_price != "" ? cnf($min_price) : ""); ?>">
                    <input type="hidden" name="min_price" id="min-price" class="form-control" value="<?php echo $min_price; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('مدت زمان اعتبار امتیاز (روز)')?></label>
                    <input type="text" name="expire" data-id="expire" class="form-control money-filter" placeholder="<?=__('اعتبار')?>..." value="<?php echo ($expire != "" ? cnf($expire) : ""); ?>">
                    <input type="hidden" name="expire" id="expire" class="form-control" value="<?php echo $expire; ?>">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('اعضای باشگاه')?></label>
                    <select name="people" id="people" class="form-select">
                        <option value="1" <?php echo $people == 1 ? "selected" : "" ?>><?=__('همه اشخاص')?></option>
                        <option value="0" <?php echo $people == 0 ? "selected" : "" ?>><?=__('انتخابی')?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button type="button" id="store-club" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                    <button type="reset" id="delete-club" class="btn btn-label-secondary"><?=__('حذف مقادیر')?></button>
                </div>
            </div>
            <?php
        }
    }

    public function showRating($people, $boolean = false)
    {
        if($people->count() > 0) {
            ?>
            <table id="rating-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th><?=__('نام')?></th>
                        <th><?=__('امتیاز کل')?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($people as $person) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $boolean ? $person->person->getFullName() : $person->getFullName(); ?></td>
                        <td><?php echo $boolean ? $person->sum : $person->rate; ?></td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                </tbody>
            </table>
            <?php
        } else {
            ?>
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
            <?php
        }
    }

    public function storeClub(Person $person, $price, $object_type, $object_id)
    {
        $rate = round($price / $this->price, 2);
        if ($rate != 0 && ($this->all_people == 1 || $person->club == 1)) {
            $log = ClubLog::create([
                'person_id' => $person->id,
                'rate' => $rate,
                'balance_rate' => $person->rate + $rate,
                'object_type' => $object_type,
                'object_id' => $object_id,
                'price' => $price
            ]);

            $wallet_price = $rate * $this->rate;
            $expire_date = today()->addDays($this->expire);
            Wallet::create([
                'person_id' => $person->id,
                'balance' => $person->wallet_value + $wallet_price,
                'price' => $wallet_price,
                'final_price' => $wallet_price,
                'description' => 'باشگاه مشتریان کد ' . $log->id,
                'expire' => $expire_date
            ]);
            $person->rate += $rate;
            $person->wallet_value += $wallet_price;
            $person->save();
            return $wallet_price;
        }
        return 0;
    }
}
