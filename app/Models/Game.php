<?php

namespace App\Models;

use App\Http\Controllers\GameController;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Station;
use App\Models\Adjective;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\MyModels\Main;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Main
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];



    public function adjective_values()
    {
        $collect = collect(json_decode($this->adjective));
        return $collect->map(function ($value) {
            return Adjective::find($value)->name;
        })->toArray();
    }

    function getLastUId()
    {
        return GameMeta::where('g_id', $this->id)->latest('u_id')->first()->u_id;
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function counter()
    {
        return $this->hasOne(CounterItem::class, 'g_id');
    }

    public function metas()
    {
        return $this->hasMany(GameMeta::class, "g_id");
    }

    public function factor()
    {
        return $this->hasOne(Factor::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
   
    public function payments()
    {
        return $this->morphMany(Payment::class, 'object');
    }

    public function ClubLogs()
    {
        return $this->morphMany(ClubLog::class, 'object');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function calcPrice($using_sharj = true, $updateGame = false)
    {
        // dump('11');
        $normal_min = 0;
        $normal_price = 0;
        $vip_min = 0;
        $vip_price = 0;
        $extra_min = 0;
        $extra_price = 0;
        $entrance = 0;
        $game_price = 0;
        $used_sharj = 0;
        $person = $this->person;
        $pastM = 0;
        $pastMVip = 0;
        $vipEntrance = 0;
        $NormalEntrance = 0;

        foreach ($this->metas as $meta) {
            $c = $meta->calcPrice(null, $pastM);

            if (array_key_exists('message', $c)) {
                return ['message' => $c['message']];
            }
        }

        $meta = new GameMeta;
        $setGloabal = $meta->showIndex2($this->id);
        $vip_price = $GLOBALS['vip_price'];
        $normal_price = $GLOBALS['normal_price'];
        $vip_min =   $GLOBALS['vip_min'];
        $normal_min = $GLOBALS['normal_min'];
        $entrance = $GLOBALS['entrances'];
        $game_price += ($GLOBALS['total_price'] + ($entrance));
        if ($person?->isNotExpired() && $using_sharj) {

            $total_min = ($normal_min) + $vip_min;
            if ($person->sharj_type == 'times') {
                if ($person->sharj >= $this->count) {
                    $used_sharj = $this->count;
                    $normal_price = 0;
                    $vip_price = 0;
                    $entrance = 0;
                    $game_price = 0;
                }
            } else {
                if ($person->sharj >= $total_min) {
                    $used_sharj = $total_min;
                    $normal_price = 0;
                    $vip_price = 0;
                    $entrance = 0;
                    $game_price = 0;
                } else if ($person->sharj < $total_min) {
                    $used_sharj = $person->sharj;
                    $extra_min = $total_min - $used_sharj;
                    $meta = new GameMeta;
                    $meta->key = "extra";
                    $meta->g_id = $this->id;
                    $meta->value = $this->count;

                    $ex = $meta->calcPrice($extra_min);

                    if (array_key_exists('message', $ex)) {
                        return ['message' => $ex['message']];
                    }

                    $normal_price = 0;
                    $vip_price = 0;
                    $extra_price = $ex['price'];
                    $entrance = $ex['entrance'] * $this->count;
                    $game_price = $ex['price'] +  $entrance;
                }
            }
        }

        $factor_price = $this->factor?->total_price ?? 0;
        $offer_price = $this->calcOffer($game_price, $factor_price);
        $setting = new Setting;
        $price = $game_price + $factor_price;
        $a_offer_price = $game_price + $factor_price - $offer_price;
        $final_price_before_round = $a_offer_price;
        $a_offer_price = makeRound($a_offer_price);
        $final_price_after_round =  $a_offer_price;

        if (($vat = $setting->getSetting('vat')) >= 0) {
            $vatPrice = $a_offer_price * ($vat / 100);
            $a_offer_price += $vatPrice;
        }

        // dump( $final_price_before_round, $a_offer_price);
        $result = [
            'normal_min' => $normal_min,
            'normal_price' => $normal_price,
            'vip_min' => $vip_min,
            'vip_price' => $vip_price,
            'extra_min' => $extra_min,
            'extra_price' => $extra_price,
            'used_sharj' => $used_sharj,
            'login_price' => $entrance,
            'game_price' => $game_price,
            'factor_price' => $factor_price,
            'price' => $price,
            'vat_rate' => $vat,
            'vat_price' => $vatPrice ?? 0,
            'final_price_before_round' => $final_price_before_round,
            'final_price_after_round' => $final_price_after_round,
            'offer_price' => $offer_price,
            'a_offer_price' => $a_offer_price,
        ];
        if ($updateGame) {
            $data['sharj_package'] = $person?->package?->name;
            $data['initial_sharj'] = $person?->sharj;
            $this->updateGame($result, $data);
        }
        return $result;
    }

    public function updateGame($prices, $data)
    {
        $out_time = now()->format("H:i:s");
        $out_date = today();

        // dd($using_sharj, $prices );
        $offer = Offer::find($this->offer_code);
        $offer_price = $this->offer_price;
        $offer_name = $this->offer_name == 'disable' ? null : $this->offer_name;
        if ($offer) {
            $offer_price = $prices['offer_price'];
            $offer_name = $offer->name;
        }
        $this->update([
            "out_time" => $out_time,
            "out_date" => $out_date,
            "total" => $prices["normal_min"],
            "total_vip" => $prices["vip_min"],
            "extra" => $prices["extra_min"],
            "total_price" => $prices["normal_price"],
            "total_vip_price" => $prices["vip_price"],
            "extra_price" => $prices["extra_price"],
            "total_shop" => $prices["factor_price"],
            "login_price" => $prices["login_price"],
            "game_price" => $prices["game_price"],
            "final_price" => $prices["a_offer_price"],
            'offer_price' => $offer_price,
            'offer_name' => $offer_name,
            'vat_rate' => $prices['vat_rate'],
            'vat_price' => $prices['vat_price'],
            'sharj_package' => $data['sharj_package'],
            'initial_sharj' => $data['initial_sharj'],
            'used_sharj' => $prices['used_sharj'],
            'final_price_after_round' => $prices['final_price_after_round'],
            'final_price_before_round' => $prices['final_price_before_round'],
        ]);
    }

    public function getCounter()
    {
        $counter = $this->counter;
        if (is_null($counter)) {
            return '<span class="badge bg-danger">'.__('ندارد').'</span>';
        } else {
            return '<span class="badge bg-success">'.__('دارد').'</span>';
        }
    }

    public function calcOffer($game_price, $factor_price = 0)
    {
        $all = $game_price + $factor_price;
        $offer = Offer::find($this->offer_code);
        $offer_calc = $offer?->calc ?? $this->offer_calc;
        switch ($offer_calc) {
            case "all":
                $p = $all;
                break;
            case "game":
                $p = $game_price;
                break;
            case "factor":
                $p = $factor_price;
                break;
        }

        if (!$offer) {
            $offer_price = $this->offer_price;
        } else {
            if ($offer->type == "مبلغ") {
                $offer_price = $offer->min_price <= $p ? $offer->per : 0;
            } else if ($offer->type == "درصد") {
                $offer_price = $offer->min_price <= $p ? ($offer->per * $p) / 100 : 0;
            }
        }

        return $offer_price;
    }

    public function crud(Section $section, Station $station, Adjective $adjective, Counter $counter)
    {
        $setting = new Setting;
        $default_section = $setting->getSetting('default_section');
?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo __('ثبت ورود'); ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-12 form-group d-flex align-items-center justify-content-start">
                    <div class="form-check my-0 mx-1">
                        <input name="radio" class="form-check-input" type="radio" id="defaultRadio1" value="person" checked>
                        <label class="form-check-label" for="defaultRadio1"><?php echo __('ورود تکی'); ?></label>
                    </div>
                    <div class="form-check my-0 mx-1">
                        <input name="radio" class="form-check-input" type="radio" id="defaultRadio2" value="group">
                        <label class="form-check-label" for="defaultRadio2"><?php echo __('ورود گروهی'); ?></label>
                    </div>
                </div>
            </div>
            <div id="single-entrance">
                <form id="p-search-game">
                    <div class="row">
                        <div class="col-md-10 mb-3">
                            <label class="form-label"><?php echo __('انتخاب شخص (نام و نام خانوادگی / موبایل / اشتراک / کد کارت)'); ?></label>
                            <input type="search" id="search-person-input" class="form-control" placeholder="<?php echo __('انتخاب شخص'); ?>...">
                        </div>
                        <div class="col-md-2 mb-3 d-flex">
                            <button type="submit" class="btn btn-success form-control align-self-end"><?php echo __('جستجو'); ?></button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-10">
                        <div id="search-result" class="list-group"></div>
                    </div>
                </div>
                <hr>
                <form id="game-form">
                    <h4><?php echo __('اطلاعات شخص'); ?></h4>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('نام'); ?> <span class="text-danger">*</span></label>
                            <input type="text" id="person-name" data-id="person-name" class="form-control checkEmpty" placeholder="<?php echo __('نام'); ?>...">
                            <div class="invalid-feedback" data-id="person-name" data-error="checkEmpty"></div>
                            <input type="hidden" id="person-id" value="0">
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('نام خانوادگی'); ?> <span class="text-danger">*</span></label>
                            <input type="text" id="person-family" data-id="person-family" class="form-control checkEmpty" placeholder="<?php echo __('نام خانوادگی') ?>...">
                            <div class="invalid-feedback" data-id="person-family" data-error="checkEmpty"></div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('جنسیت'); ?> <span class="text-danger">*</span></label>
                            <select name="" id="person-gender" data-id="person-gender" class="form-select checkEmpty">
                                <option value="0"><?php echo __('دختر'); ?></option>
                                <option value="1"><?php echo __('پسر'); ?></option>
                            </select>
                            <div class="invalid-feedback" data-id="person-gender" data-error="checkEmpty"></div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('موبایل'); ?> <span class="text-danger">*</span></label>
                            <input type="text" id="person-mobile" data-id="person-mobile" class="form-control just-numbers checkEmpty checkMobile" placeholder="<?php echo __('موبایل'); ?>..." maxlength="11">
                            <div class="invalid-feedback" data-id="person-mobile" data-error="checkEmpty"></div>
                            <div class="invalid-feedback" data-id="person-mobile" data-error="checkMobile"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('تاریخ تولد'); ?></label>
                            <input type="text" id="person-birth" class="form-control date-mask pwt-datepicker-input-element" placeholder="<?php echo __('تاریخ تولد'); ?>...">
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('کد ملی'); ?></label>
                            <input type="text" id="person-nationalcode" data-id="person-nationalcode" class="form-control just-numbers checkNationalCode" placeholder="<?php echo __('کد ملی'); ?>...">
                            <div class="invalid-feedback" data-id="person-nationalcode" data-error="checkNationalCode"></div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('کد اشتراک'); ?></label>
                            <input type="text" id="person-regcode" class="form-control just-numbers" placeholder="<?php echo __('کد اشتراک'); ?>...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <label class="form-label"><?php echo __('آدرس'); ?></label>
                            <input type="text" id="person-address" class="form-control" placeholder="<?php echo __('آدرس'); ?>...">
                        </div>
                    </div>
                    <hr>
                    <h4><?php echo __('اطلاعات ورود'); ?></h4>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('زمان ورود') ?></label>
                            <input type="text" readonly id="in-dateTime" data-id="in" class="form-control datetime-mask-current" placeholder="<?=__('زمان حال')?>">
                            <input type="text" readonly value="<?=CurrentTime()?>" data-id="in" class="form-control datetime-mask-custome" style="display:none" placeholder="<?=__('زمان حال')?>">
                            <input type="hidden" id="in-dateTime-value">
                            <div class="invalid-feedback" data-id="in" data-error="checkEmpty"></div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('بخش'); ?></label>
                            <?php
                            if ($section->getSelect()->count() > 0) {
                            ?>
                                <select name="" id="section-id" data-id="section-id" class="form-select checkEmpty">
                                    <?php
                                    foreach ($section->getSelect() as $sectionRow) { ?>
                                        <option value="<?php echo $sectionRow->id; ?>" <?php echo ($sectionRow->id == $default_section) ? "selected" : ""; ?>><?php echo $sectionRow->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback" data-id="section-id" data-error="checkEmpty"></div>
                            <?php
                            } else {
                            ?>
                                <div><span class="badge bg-danger"><?php echo __('بدون بخش'); ?></span></div>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('میز / ایستگاه'); ?></label>
                            <?php
                            if ($station->getSelect()->count() > 0) {
                            ?>
                                <select name="" id="station-id" class="form-select">
                                    <option value=""><?php echo __('هیچکدام'); ?></option>
                                    <?php
                                    foreach ($station->getSelect() as $stationRow) { ?>
                                        <option value="<?php echo $stationRow->id; ?>"><?php echo $stationRow->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            <?php
                            } else {
                            ?>
                                <div><span class="badge bg-danger"><?php echo __('بدون میز / ایستگاه'); ?></span></div>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('شمارنده'); ?></label>
                            <?php
                            if ($counter->getSelect()->count() > 0) {
                            ?>
                                <select name="" id="counter-id" class="form-select">
                                    <option value=""><?php echo __('هیچکدام'); ?></option>
                                    <?php
                                    foreach ($counter->getSelect() as $counterRow) { ?>
                                        <option value="<?php echo $counterRow->id; ?>"><?php echo $counterRow->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            <?php
                            } else {
                            ?>
                                <div><span class="badge bg-danger"><?php echo __('بدون شمارنده'); ?></span></div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('نوع ورود'); ?></label>
                            <select name="" id="rate" class="form-select">
                                <option value="normal"><?php echo __('عادی'); ?></option>
                                <option value="vip"><?php echo __('ویژه'); ?></option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?= __('تعداد'); ?></label>
                            <input type="number" id="count" class="form-control" placeholder="<?php __('تعداد'); ?>..." value="1">
                        </div>
                        <div class="col-1 mb-3 d-flex">
                            <label class="switch switch-success switch-lg align-self-end">
                                <input type="checkbox" class="switch-input" id="seperate">
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"><?php echo __('مجزا'); ?></span>
                                    <span class="switch-off"><?php echo __('یکجا'); ?></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('امانتی ها'); ?></label>
                            <?php
                            if ($adjective->getSelect()->count() > 0) {
                            ?>
                                <select name="" id="adjectives" class="form-select select2-g" multiple>
                                    <?php
                                    foreach ($adjective->getSelect() as $adjectiveRow) { ?>
                                        <option value="<?php echo $adjectiveRow->id; ?>"><?php echo $adjectiveRow->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            <?php
                            } else {
                            ?>
                                <div>
                                    <span class="badge bg-danger d-block"><?php echo __('بدون امانتی'); ?></span>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('مبلغ پیش پرداخت'); ?></label>
                            <input type="text" id="deposit" class="form-control just-numbers" placeholder="<?php echo __('مبلغ پیش پرداخت'); ?>...">
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('نوع پیش پرداخت'); ?></label>
                            <select name="" id="deposit-type" class="form-select">
                                <option value=""><?php echo __('انتخاب'); ?></option>
                                <?php foreach (PaymentType::all() as $type) { ?>
                                    <option value="<?= $type->name ?>"><?= $type->label ?></option>
                                <?php } ?>

                            </select>
                        </div>
                    </div>
                    <hr>
                    <h4><?php echo __('همراهی'); ?></h4>
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('نام'); ?></label>
                            <input type="text" id="accompany-name" class="form-control" placeholder="<?php echo __('نام'); ?>...">
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('شماره موبایل'); ?></label>
                            <input type="text" id="accompany-mobile" class="form-control just-numbers" placeholder="<?php echo __('شماره موبایل'); ?>..." maxlength="11">
                        </div>
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label class="form-label"><?php echo __('نسبت'); ?></label>
                            <select name="" id="accompany-relation" class="form-select">
                                <option value=""><?php echo __('انتخاب'); ?></option>
                                <option><?php echo __('پدر'); ?></option>
                                <option><?php echo __('مادر'); ?></option>
                                <option><?php echo __('خواهر'); ?></option>
                                <option><?php echo __('برادر'); ?></option>
                                <option><?php echo __('مادربزرگ'); ?></option>
                                <option><?php echo __('پدربزرگ'); ?></option>
                                <option><?php echo __('سایر'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3 text-center">
                            <button type="submit" id="store-game" class="btn btn-success me-sm-3 me-1"><?php echo __('ثبت ورود'); ?></button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="group-entrance" style="display: none;">
                <form id="g-search-game">
                    <div class="row">
                        <div class="col-md-10 mb-3">
                            <label class="form-label"><?php echo __('انتخاب گروه'); ?></label>
                            <input type="search" id="search-group-input" class="form-control" placeholder="<?php echo __('انتخاب گروه'); ?>...">
                        </div>
                        <div class="col-md-2 mb-3 d-flex">
                            <button type="submit" class="btn btn-success form-control align-self-end"><?php echo __('جستجو'); ?></button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-10 mb-3">
                        <div id="search-group-result" class="list-group"></div>
                    </div>
                </div>
                <hr>
                <form id="g-game-form">
                    <h4><?php echo __('اطلاعات ورود'); ?></h4>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label"><?php echo __('بخش'); ?></label>
                            <?php
                            if ($section->getSelect()->count() > 0) {
                            ?>
                                <select name="" id="g-section-id" data-id="g-section-id" class="form-select checkEmpty">
                                    <?php
                                    foreach ($section->getSelect() as $sectionRow) { ?>
                                        <option value="<?php echo $sectionRow->id; ?>"><?php echo $sectionRow->name; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback" data-id="g-section-id" data-error="checkEmpty"></div>
                            <?php
                            } else {
                            ?>
                                <div>
                                    <span class="badge bg-danger"><?php echo __('بدون بخش'); ?></span>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </form>
                <div class="row border mx-1" id="group-people"></div>
                <div class="row">
                    <div class="col-12 mb-3 text-center">
                        <input type="hidden" id="g-group-id" value="0">
                        <button type="submit" id="store-group-game" class="btn btn-success me-sm-3 me-1"><?php echo __('ثبت ورود'); ?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function showIndex()
    {
        $active_games = Game::where("status", 0)->orderByDesc('created_at')->get();
        if ($active_games->count() > 0) {
        ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف'); ?></th>
                        <th><?php echo __('نام و نام خانوادگی'); ?></th>
                        <th><?php echo __('کد اشتراک'); ?></th>
                        <th><?php echo __('تعداد'); ?></th>
                        <th><?php echo __('ساعت ورود'); ?></th>
                        <th><?php echo __('شمارنده') ?></th>
                        <th><?php echo __('بخش'); ?></th>
                        <th><?php echo __('ایستگاه'); ?></th>
                        <th><?php echo __('عملیات'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($active_games as $key => $game) {
                    ?>
                        <tr <?php echo $game->person?->getMeta("person-info") != "" ? 'class="bg-label-secondary"' : "" ?>>
                            <td><?php echo $key + 1 ?></td>
                            <td><?php echo $game->person_fullname ?></td>
                            <td><?php echo $game->person?->reg_code ?? '-' ?></td>
                            <td><?php echo $game->count ?></td>
                            <td><?php echo $game->in_time ?></td>
                            <td><?php echo $game->getCounter() ?></td>
                            <td><?php echo $game->section_name ?></td>
                            <td><?php echo $game->station_name ?? '<span class="badge bg-danger">هیچکدام</span>' ?></td>
                            <td>
                                <button data-bs-toggle="modal" data-bs-target="#game-modal" data-id="<?php echo $game->id ?>" class="load-game btn btn-info">
                                    <i class="bx bx-check" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{ __('استعلام') }}"></i>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#person-info-modal" class="btn btn-primary crud-person-info" data-p_id="<?php echo $game->person_id ?>">
                                    <i class="bx bx-question-mark" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('توضیحات') }}"></i>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#factor-modal" class="btn btn-warning crud-factor" data-g_id="<?php echo $game->id ?>" data-f_type="game">
                                    <i class="bx bx-coffee" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-warning" title="{{ __('فروشگاه') }}"></i>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#changes-modal" class="btn btn-danger crud-changes" data-g_id="<?php echo $game->id ?>">
                                    <i class="bx bx-plus-circle" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('تغییرات') }}"></i>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#changes-modal" class="btn btn-danger crud-changes" data-g_id="<?php echo $game->id ?>">
                                    <i class="bx bx-timer" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('شمارنده') }}"></i>
                                </button>
                                <button data-bs-toggle="modal" data-bs-target="#offer_modal1334" class="btn btn-dark crud-offer" data-g_id="<?php echo $game->id ?>">
                                    <i class="bx bx-minus-circle" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="{{ __('تخفیف') }}"></i>
                                </button>
                                <?php
                                if (!is_null($game->accompany_name) || !is_null($game->accompany_mobile)) { ?>
                                    <button data-bs-toggle="modal" data-bs-target="#accompany-modal" class="btn btn-success accompany-modal" data-g_id="<?php echo $game->id ?>">
                                        <i class="bx bx-user" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-custom-class="tooltip-success" title="{{ __('همراه') }}"></i>
                                    </button>
                                <?php
                                } ?>
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
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?php echo __('شخصی در مجموعه حضور ندارد.'); ?></div>
                </div>
            </div>
        <?php
        }
    }

    public function loadGame($g_id)
    {
        $meta = new GameMeta;
        $setGloabal = $meta->showIndex2($g_id);

        $setting = new Setting;
        $game = Game::find($g_id);
        $person = $game->person;
        if (!($game->offer_code) and $game->offer_name != 'disable' and $offerCode = $setting->getSetting('global_offer')) {
            $game->update([
                'offer_code' => $offerCode
            ]);
        }
        if (!$person) {
            return response()->json([
                'message' => 'شخص یافت نشد.'
            ], 404);
        }
        $calc_price = $game->calcPrice(true, true);
        if (array_key_exists('message', $calc_price)) {
            return "<div class='alert alert-danger text-center m-0'>" . $calc_price['message'] . "</div>";
        }

        $wallet = new Wallet;
        $payment_type = new PaymentType;
        $offer = Offer::find($game->offer_code);
        $offer_name = $offer ? '(' . $offer->name . ')' : '';
        // dump($calc_price["a_offer_price"]);
        $pay = $calc_price["a_offer_price"] >= $game->deposit ? $calc_price["a_offer_price"] - $game->deposit : 0;

        $m = (($setting->getSetting('load_game_type') == 'modal') || ($setting->getSetting('load_game_type') == ''));
        if ($m) { ?>
            <button type="button" id="close-game-modal" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <button type="button" id="delete-game" data-id="<?=$game->id?>" class="btn btn-sm btn-danger">
                <i class="bx bx-trash"></i>
            </button>
            <div class="modal-body table-responsive">
            <?php } ?>
            <div id="bill-print-area" class="w-100 row mx-0">
                <div class="row mx-0">
                    <div class="col-12">
                        <h3 class="text-center">
                            <?php echo $setting->getSetting("center_name") ?>
                            <?php
                            if ($setting->getSetting('avatar') != "") {
                            ?>
                                <img src="<?php echo $setting->getSetting('avatar') ?>" style="width: 50px; height: auto">
                            <?php
                            }
                            ?>
                        </h3>
                    </div>
                </div>
                <?php if (strpos('data-cke-filler="true"',$bill_header=$setting->getSetting('bill_header'))) { ?>
                    <div class="row mx-0 mb-3 bill-header">
                        <div class="col-12">
                            <p class="text-center my-auto"><?php echo $bill_header ?></p>
                        </div>
                    </div>
                <?php } ?>
                <div class="">
                    <table class="table table-bordered customer-table text-center">
                        <tr>
                            <th class="text-center">
                                <a href="<?php echo route('reportPerson', ['id' => $person->id]) ?>" target="_blank">
                                    <h6 class="m-0"><b><?php echo __('مشتری'); ?>: </b> <?php echo $game->person_fullname; ?></h6>
                                </a>
                            </th>
                            <th class="text-center">
                                <h6 class="m-0"><b><?php echo __('موبایل'); ?>: </b> <?php echo $game->person_mobile; ?></h6>
                            </th>
                            <th class="text-center">
                                <h6 class="m-0"><b><?php echo __('تاریخ'); ?>: </b> <?php echo DateFormat($game->in_date); ?></h6>
                            </th>
                            <th class="text-center">
                                <h6 class="m-0"><b><?php echo __('کد'); ?>: </b> <?php echo $game->id; ?></h6>
                            </th>
                        </tr>
                    </table>
                </div>
                <?php
                if ($person->getMeta('person-info') != "") { ?>
                    <div class="alert alert-danger text-center no-print"><?php echo $person->getMeta('person-info'); ?></div>
                <?php
                }
                if (array_key_exists('message', $calc_price)) { ?>
                    <div class="alert alert-danger text-center">
                        <?php echo $calc_price["message"] ?>
                    </div>
                <?php
                } else {
                ?>
                    <div id="meta-result" class="mt-2 mb-2"><?php echo $setGloabal; ?></div>
                    <div class="">
                        <table class="table table-bordered table-success checkout-table text-center mb-1">
                            <tbody>
                                <tr>
                                    <th>
                                        <h4 class="m-0"><?php echo __('جمع عادی'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo $calc_price["normal_min"] . minLabel(); ?></h4>
                                    </td>
                                    <th>
                                        <h4 class="m-0"><?php echo __('مبلغ عادی'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo cnf($calc_price["normal_price"]) . curr() ?></h4>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <th>
                                        <h4 class="m-0"><?php echo __('جمع ویژه'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo $calc_price["vip_min"] . minLabel() ?></h4>
                                    </td>
                                    <th>
                                        <h4 class="m-0"><?php echo __('مبلغ ویژه'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo cnf($calc_price["vip_price"]) . curr() ?></h4>
                                    </td>
                                </tr>
                                <?php
                                if ($calc_price["extra_min"] > 0) { ?>
                                    <tr>
                                        <th>
                                            <h4 class="m-0"><?php echo __('جمع مازاد'); ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($calc_price["extra_min"]) . minLabel() ?></h4>
                                        </td>
                                        <th>
                                            <h4 class="m-0"><?php echo __('مبلغ مازاد'); ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($calc_price["extra_price"]) . curr() ?></h4>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                                <tr class="table-success">
                                    <th colspan="2">
                                        <h4 class="m-0"><?php echo __('مبلغ ورودی'); ?>:</h4>
                                    </th>
                                    <td colspan="2">
                                        <h4 class="m-0"><?php echo cnf($calc_price["login_price"]) . curr() ?></h4>
                                    </td>
                                </tr>
                                <tr class="border-bottom-2">
                                    <th colspan="100%">
                                        <h4 class="m-0"><?php echo __('مجموع بخش:'); ?> <?= cnf($calc_price['game_price']) . curr() ?> </h4>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if ($calc_price["used_sharj"] > 0) { ?>
                        <div class="">
                            <table class="table table-bordered table-primary checkout-table text-center m-0">
                                <tbody>
                                    <tr class="bg-warning">
                                        <th>
                                            <h4 class="m-0"><?php echo __('شارژ استفاده شده'); ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($calc_price["used_sharj"]) . minLabel($person->sharj_type) ?></h4>
                                        </td>
                                        <th>
                                            <h4 class="m-0"><?php echo __('شارژ باقی مانده') ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($person->sharj - $calc_price["used_sharj"]) . minLabel($person->sharj_type) ?></h4>
                                        </td>
                                    </tr>
                                    <tr class="bg-warning">
                                        <th colspan="2">
                                            <h4 class="m-0"><?php echo __('بسته در حال استفاده'); ?>:</h4>
                                        </th>
                                        <td colspan="2">
                                            <h4 class="m-0 d-inline-block"><?php echo $person->package?->name ?></h4>
                                            <small> (<?= __('انقضا') . ': ' . dateFormat($person->expire) ?>)</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    }
                    if ($game->factor) { ?>
                        <div id="g-factor-result" class="mt-1">
                            <?php $game->factor?->showBodies("load-game"); ?>
                        </div>
                    <?php
                    }
                    ?>
                    <div class=" mt-1">
                        <table class="table table-bordered checkout-table table-info">
                            <!--tr>
                                <th>جمع فروشگاه: </th>
                                <td><?php //echo cnf($game->factor?->total_price)
                                    ?></td>
                            </tr-->
                            <tr>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('تخفیف'); ?>:</h4>
                                    <span><?php echo $offer_name ?></span>
                                </th>
                                <td class="text-center">
                                    <h4 class="m-0"><?php echo cnf($calc_price["offer_price"]) . curr() ?></h4>
                                </td>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('جمع کل') ?>:</h4>
                                </th>
                                <td class="text-center">
                                    <?php if ($calc_price['final_price_before_round'] != $calc_price['final_price_after_round']) { ?> <small>(<del><?= cnf($calc_price['final_price_before_round']) ?></del>)</small> <?php } ?>
                                    <h4 class="m-0 d-inline-block">
                                        <?= cnf($calc_price['final_price_after_round']) . curr() ?>
                                    </h4>
                                </td>
                            </tr>
                            <?php
                            if ($calc_price['vat_rate'] >= 0) { ?>
                                <tr>
                                    <th class="text-center">
                                        <h4 class="m-0 d-inline-block"><?php echo __('مالیات'); ?>:</h4> <small>(<?= $calc_price['vat_rate'] ?>%)</small>

                                    </th>
                                    <td class="text-center">
                                        <h4 class="m-0"><?= cnf($calc_price['vat_price']) . curr() ?></h4>
                                    </td>
                                    <th class="text-center">
                                        <h4 class="m-0 d-inline-block"><?php echo __('مبلغ نهایی'); ?>:</h4>
                                    </th>
                                    <td class="text-center">
                                        <h4 class="m-0"><?= cnf($calc_price['a_offer_price']) . curr() ?></h4>
                                    </td>
                                </tr>
                            <?php
                            } ?>
                            <tr>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('پیش پرداخت'); ?>:</h4>
                                </th>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-2">

                                        <input type="hidden" id="deposit" data-g_id="<?php echo $game->id ?>" class="form-control" value="<?php echo $game->deposit ?>">
                                        <div class="col-auto">
                                            <input type="text" data-id="deposit" class="form-control m-auto no-print money-filter" value="<?php echo cnf($game->deposit) ?>" placeholder="پیش پرداخت...">
                                        </div>
                                        <div class="col-auto no-print">
                                            <select id="deposit-type" class="form-select">
                                                <option value=""><?php echo __('روش پرداخت'); ?></option>
                                                <?php foreach (PaymentType::all() as $type) { ?>
                                                    <option value="<?= $type->name ?>"><?= $type->label ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-warning no-print update-deposit btn-sm"><?=__('اعمال')?></button>
                                    </div>
                                    <h4 class="m-0 just-print"><?php echo cnf($game->deposit) ?></h4>
                                </td>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('قابل پرداخت'); ?>:</h4>
                                </th>
                                <td class="text-center">
                                    <h4 class="m-0"><?php echo cnf($pay) . curr() ?></h4>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center" colspan="2">
                                    <h4 class="m-0"><?php echo __('حساب قبلی'); ?>:</h4>
                                </th>
                                <td class="text-center" colspan="2">
                                    <h4 class="m-0"><?php $person->getBalance() ?></h4>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    if (json_decode($game->adjective)) { ?>
                        <div class="alert bg-warning text-black">
                            <strong><?php echo __('امانتی ها'); ?>: </strong> <?= implode("، ", $game->adjective_values()) ?>
                        </div>
                    <?php
                    } ?>
                    <h3 class="text-center m-0 p-0"><?php echo $setting->getSetting("bill_sign") ?></h3>
                    <?php $payment_type->loadPaymentTypes($pay, $person?->id, true); ?>

                    <div class="row mx-0 mb-3 no-print">
                        <div class="col-md-12">
                            <div class="form-check text-right">
                                <label class="form-check-label font-weight-bold">
                                    <?php echo __('عدم ارسال پیامک خروج'); ?>
                                    <input type="checkbox" class="form-check-input" value="1" id="no-logout-message">
                                </label>
                            </div>
                            <div class="form-check text-right">
                                <label class="form-check-label font-weight-bold">
                                    <?php echo __('عدم ارسال پیامک نظرسنجی'); ?>
                                    <input type="checkbox" class="form-check-input" value="1" id="no-feedback-message">
                                </label>
                            </div>
                            <div class="form-check text-right">
                                <label class="form-check-label font-weight-bold">
                                    <?php echo __('عدم ارسال پیامک باشگاه'); ?>
                                    <input type="checkbox" class="form-check-input" value="1" id="no-club-message">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-0 mb-3 no-print">
                        <div class="text-center col-md-12">
                            <button class="btn btn-danger close-game" data-g_id="<?php echo $g_id ?>"><?php echo __('بستن صورتحساب'); ?></button>
                            <button id='print-bill' data-id='<?= $g_id ?>' href="/print/<?= $g_id ?>" class="btn btn-info "><?php echo __('چاپ'); ?></button>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php if ($m) { ?>
            </div>
        <?php
            }
        }

        public function loadGameDetails($g_id)
        {
            $meta = new GameMeta;
            $setGloabal = $meta->showIndexLog($g_id);
            $setting = new Setting;
            $game = Game::find($g_id);
            $person = $game->person;

            if (!$person) {
                return response()->json([
                    'message' => 'شخص یافت نشد.'
                ], 404);
            }

            $wallet = new Wallet;
            $payment_type = new PaymentType;
            $offer = Offer::find($game->offer_code);
            $offer_name = $offer ? '(' . $offer->name . ')' : '';
            // dump($calc_price["a_offer_price"]);
            // $pay = $calc_price["a_offer_price"] >= $game->deposit ? $calc_price["a_offer_price"] - $game->deposit : 0;

            $m = (($setting->getSetting('load_game_type') == 'modal') || ($setting->getSetting('load_game_type') == ''));
            if ($m) { ?>
            <button type="button" id="close-game-modal" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body table-responsive">
            <?php } ?>
            <div id="bill-print-area" class="w-100 row mx-0">
                <div class="row mx-0">
                    <div class="col-12">
                        <h3 class="text-center">
                            <?php echo $setting->getSetting("center_name") ?>
                            <?php
                            if ($setting->getSetting('avatar') != "") {
                            ?>
                                <img src="<?php echo $setting->getSetting('avatar') ?>" style="width: 50px; height: auto">
                            <?php
                            }
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="row mx-0 mb-3 bill-header">
                    <div class="col-12">
                        <p class="text-center my-auto"><?php echo $setting->getSetting('bill_header') ?></p>
                    </div>
                </div>
                <div class="">
                    <table class="table table-bordered customer-table text-center">
                        <tr>
                            <th class="text-center">
                                <a href="<?php echo route('reportPerson', ['id' => $person->id]) ?>" target="_blank">
                                    <h6 class="m-0"><b><?php echo __('مشتری'); ?>: </b> <?php echo $game->person_fullname; ?></h6>
                                </a>
                            </th>
                            <th class="text-center">
                                <h6 class="m-0"><b><?php echo __('موبایل'); ?>: </b> <?php echo $game->person_mobile; ?></h6>
                            </th>
                            <th class="text-center">
                                <h6 class="m-0"><b><?php echo __('تاریخ'); ?>: </b> <?php echo DateFormat($game->in_date); ?></h6>
                            </th>
                            <th class="text-center">
                                <h6 class="m-0"><b><?php echo __('کد'); ?>: </b> <?php echo $game->id; ?></h6>
                            </th>
                        </tr>
                    </table>
                </div>
                <?php
                if ($person->getMeta('person-info') != "") { ?>
                    <div class="alert alert-danger text-center no-print"><?php echo $person->getMeta('person-info'); ?></div>
                <?php
                }
                if (false) { ?>
                    'details mode';
                <?php
                } else {
                ?>
                    <div id="meta-result" class="mt-2 mb-2"><?php echo $setGloabal; ?></div>
                    <div class="">
                        <table class="table table-bordered table-success checkout-table text-center mb-1">
                            <tbody>
                                <tr>
                                    <th>
                                        <h4 class="m-0"><?php echo __('جمع عادی'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo $game->total . minLabel(); ?></h4>
                                    </td>
                                    <th>
                                        <h4 class="m-0"><?php echo __('مبلغ عادی'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo cnf($game->total_price) . curr() ?></h4>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <th>
                                        <h4 class="m-0"><?php echo __('جمع ویژه'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo $game->total_vip . minLabel() ?></h4>
                                    </td>
                                    <th>
                                        <h4 class="m-0"><?php echo __('مبلغ ویژه'); ?>:</h4>
                                    </th>
                                    <td>
                                        <h4 class="m-0"><?php echo cnf($game->total_vip_price) . curr() ?></h4>
                                    </td>
                                </tr>
                                <?php
                                if ($game->extra > 0) { ?>
                                    <tr>
                                        <th>
                                            <h4 class="m-0"><?php echo __('جمع مازاد'); ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($game->extra) . minLabel() ?></h4>
                                        </td>
                                        <th>
                                            <h4 class="m-0"><?php echo __('مبلغ مازاد'); ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($game->extra_price) . curr() ?></h4>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                                <tr class="table-success">
                                    <th colspan="2">
                                        <h4 class="m-0"><?php echo __('مبلغ ورودی'); ?>:</h4>
                                    </th>
                                    <td colspan="2">
                                        <h4 class="m-0"><?php echo cnf($game->login_price) . curr() ?></h4>
                                    </td>
                                </tr>
                                <tr class="border-bottom-2">
                                    <th colspan="100%">
                                        <h4 class="m-0"><?php echo __('مجموع بخش:'); ?> <?= cnf($game->game_price) . curr() ?> </h4>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    if ($game->used_sharj > 0) { ?>
                        <div class="">
                            <table class="table table-bordered table-primary checkout-table text-center m-0">
                                <tbody>
                                    <tr class="bg-warning">
                                        <th>
                                            <h4 class="m-0"><?php echo __('شارژ استفاده شده'); ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($game->used_sharj) . minLabel() ?></h4>
                                        </td>
                                        <th>
                                            <h4 class="m-0"><?php echo __('شارژ باقی مانده') ?>:</h4>
                                        </th>
                                        <td>
                                            <h4 class="m-0"><?php echo cnf($game->initial_sharj - $game->used_sharj) . minLabel() ?></h4>
                                        </td>
                                    </tr>
                                    <tr class="bg-warning">
                                        <th colspan="2">
                                            <h4 class="m-0"><?php echo __('بسته در حال استفاده'); ?>:</h4>
                                        </th>
                                        <td colspan="2">
                                            <h4 class="m-0 d-inline-block"><?php echo $game->sharj_package ?></h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    }
                    if ($game->factor?->total_price) { ?>
                        <div id="g-factor-result" class="mt-1">
                            <?php $game->factor?->showBodies("load-game"); ?>
                        </div>
                    <?php
                    }
                    ?>
                    <div class=" mt-1">
                        <table class="table table-bordered checkout-table table-info">
                            <!--tr>
                                <th>جمع فروشگاه: </th>
                                <td><?php //echo cnf($game->factor?->total_price)
                                    ?></td>
                            </tr-->
                            <tr>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('تخفیف'); ?>:</h4>
                                    <span><?php echo $offer_name ?></span>
                                </th>
                                <td class="text-center">
                                    <h4 class="m-0"><?php echo cnf($game->offer_price) . curr() ?></h4>
                                </td>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('جمع کل') ?>:</h4>
                                </th>
                                <td class="text-center">
                                    <?php if ($game->final_price_before_round != $game->final_price_after_round) { ?> <small>(<del><?= cnf($game->final_price_before_round) ?></del>)</small> <?php } ?>
                                    <h4 class="m-0 d-inline-block">
                                        <?= cnf($final = $game->final_price_after_round) . curr() ?>
                                    </h4>
                                </td>
                            </tr>
                            <?php
                            if ($game->vat_rate > 0) { ?>
                                <tr>
                                    <th class="text-center">
                                        <h4 class="m-0 d-inline-block"><?php echo __('مالیات'); ?>:</h4> <small>(<?= $game->vat_rate ?>%)</small>
                                    </th>
                                    <td class="text-center">
                                        <h4 class="m-0"><?= cnf($game->vat_price) . curr() ?></h4>
                                    </td>
                                    <th class="text-center">
                                        <h4 class="m-0 d-inline-block"><?php echo __('مبلغ نهایی'); ?>:</h4>
                                    </th>
                                    <td class="text-center">
                                        <h4 class="m-0"><?= cnf($final = $game->final_price) . curr() ?></h4>
                                    </td>
                                </tr>
                            <?php
                            } ?>
                            <tr>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('پیش پرداخت'); ?>:</h4>
                                </th>
                                <td class="text-center">
                                    <h4 class="m-0"><?php echo cnf($game->deposit) . curr() ?></h4>
                                </td>
                                <th class="text-center">
                                    <h4 class="m-0"><?php echo __('قابل پرداخت'); ?>:</h4>
                                </th>
                                <td class="text-center">
                                    <h4 class="m-0"><?php echo cnf($final - $game->deposit) . curr() ?></h4>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    if (json_decode($game->adjective)) { ?>
                        <div class="alert bg-warning text-black">
                            <strong><?php echo __('امانتی ها'); ?>: </strong> <?= implode("، ", $game->adjective_values()) ?>
                        </div>
                    <?php
                    } ?>
                    <h3 class="text-center m-0 p-0"><?php echo $setting->getSetting("bill_sign") ?></h3>
                    <div class="row mx-0 mb-3 no-print">
                        <div class="text-center col-md-12">
                            <button class="btn btn-info print-bill"><?php echo __('چاپ'); ?></button>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php if ($m) { ?>
            </div>
        <?php
            }
        }

        public function showReports($games)
        {
            if ($games->count() > 0) {
        ?>
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف'); ?></th>
                        <th><?php echo __('شخص'); ?></th>
                        <th><?php echo __('بخش'); ?></th>
                        <th><?php echo __('ورود'); ?></th>
                        <th><?php echo __('خروج'); ?></th>
                        <th><?php echo __('فروشگاه'); ?></th>
                        <th><?php echo __('بازی'); ?></th>
                        <th><?php echo __('تخفیف'); ?></th>
                        <th><?php echo __('جمع'); ?></th>
                        <th><?php echo __('مدیریت'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($games as $game) {
                    ?>
                        <tr class="<?php echo !is_null($game->updated_by) ? "bg-info" : "" ?>">
                            <td><?php echo $i ?></td>
                            <td><?php echo $game->person?->getFullName() ?></td>
                            <td><?php echo $game->section?->name ?></td>
                            <td><?php echo $game->in_time . ' - ' . dateFormat($game->in_date) ?></td>
                            <td><?php echo $game->out_time . ' - ';
                                echo dateFormat($game->out_date) ?></td>
                            <td><?php echo cnf($game->total_shop ?? $game->calcPrice()["factor_price"]) ?></td>
                            <td><?php echo cnf($game->game_price ?? $game->calcPrice()["game_price"]) ?></td>
                            <td>
                                <span class="badge bg-danger">
                                    <?php echo cnf($game->offer_price ?? $game->calcPrice()["offer_price"]) ?>
                                </span>
                            </td>
                            <td><?php echo cnf($game->final_price ?? $game->calcPrice()["a_offer_price"]) ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $game->id; ?>" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-sm delete-game" data-id="<?php echo $game->id; ?>"><i class="bx bx-trash"></i></button>
                                <button type="button" class="btn btn-success btn-sm crud-meta" data-bs-toggle="modal" data-bs-target="#meta-modal" data-g_id="<?php echo $game->id ?>">مشاهده جزئیات
                                </button>
                                <?php
                                if (!is_null($game->accompany_name) || !is_null($game->accompany_mobile)) {
                                ?>
                                    <button type="button" class="btn btn-primary btn-sm accompany-modal" data-bs-toggle="modal" data-bs-target="#accompany-modal" data-g_id="<?php echo $game->id ?>">اطلاعات همراه
                                    </button>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        <?php
            } else { ?>
            <div class="alert alert-danger m-2 text-center"><?php echo __('موردی جهت نمایش موجود نیست.'); ?></div>
        <?php
            }
        }

        public function edit()
        {
            $title = __('ویرایش زمان');
            $section = new Section;
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?= __('زمان ورود'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="in" id="in" data-id="in" class="form-control checkEmpty" placeholder="1401/01/01 00:00:00" value="<?php echo timeFormat($this->in_date . " " . $this->in_time); ?>">
                    <div class="invalid-feedback" data-id="in" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('زمان خروج'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="out" id="out" data-id="out" class="form-control checkEmpty" placeholder="1401/01/01 00:00:00" value="<?php echo timeFormat($this->out_date . " " . $this->out_time); ?>">
                    <div class="invalid-feedback" data-id="out" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label"><?php echo __('مبلغ فروشگاه'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="factor_price" id="factor-price" data-id="factor-price" class="form-control checkEmpty" value="<?php echo $this->total_shop ?>" placeholder="<?php echo __('مبلغ فروشگاه'); ?>...">
                    <div class="invalid-feedback" data-id="factor-price" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label"><?php echo __('مبلغ بازی'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="game_price" id="game-price" data-id="game-price" class="form-control checkEmpty" value="<?php echo $this->game_price ?>" placeholder="<?php echo __('مبلغ بازی'); ?>...">
                    <div class="invalid-feedback" data-id="game-price" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label"><?php echo __('مبلغ تخفیف'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="offer_price" id="offer-price" data-id="offer-price" class="form-control checkEmpty" value="<?php echo $this->offer_price ?>" placeholder="<?php echo __('مبلغ تخفیف'); ?>...">
                    <div class="invalid-feedback" data-id="offer-price" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label"><?php echo __('بخش'); ?> <span class="text-danger">*</span></label>
                    <?php
                    if ($section->getSelect()->count() > 0) {
                    ?>
                        <select name="" id="section-id" data-id="section-id" class="form-select checkEmpty">
                            <?php
                            foreach ($section->getSelect() as $sectionRow) { ?>
                                <option <?=$this->section_id==$sectionRow->id?'selected':'' ?> value="<?php echo $sectionRow->id; ?>"><?php echo $sectionRow->name; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback" data-id="section-id" data-error="checkEmpty"></div>
                    <?php
                    } else {
                    ?>
                        <div>
                            <span class="badge bg-danger">بدون بخش</span>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="invalid-feedback" data-id="section-id" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button type="button" id="update-game" data-id="<?php echo $this->id ?>" class="btn btn-success me-sm-3 me-1"><?php __('ثبت اطلاعات'); ?></button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php __('انصراف'); ?></button>
                </div>
            </div>
        </div>
    <?php
        }

        public function showAccompany()
        {
    ?>
        <table class="table table-hover border-top" id="accompany-info">
            <thead>
                <th><?php echo __('نام همراه'); ?></th>
                <th><?php echo __('موبایل'); ?></th>
                <th><?php echo __('نسبت'); ?></th>
            </thead>
            <tbody>
                <td><?php echo $this->accompany_name ?></td>
                <td><?php echo $this->accompany_mobile ?></td>
                <td><?php echo $this->accompany_relation ?></td>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-12 text-center">
                <button class="btn btn-info print-accompany"><i class="bx bx-printer"></i> <?php echo __('چاپ'); ?></button>
            </div>
        </div>
<?php
        }
    }
