<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Factor extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $guarded = [];

    public function bodies()
    {
        return $this->hasMany(FactorBody::class);
    }

    public function getBodiesStringAttribute()
    {
        return $this->bodies->map(function ($item) {
            return $item->product_name . "($item->count)" . "(" . cnf($item->product_price) . ")";
        })->join(', ');
    }

    public function getSubmitDateAttribute()
    {
        return persianTime($this->created_at);
    }
    public function getTotalPriceFormatAttribute()
    {
        return price($this->total_price);
    }
    public function getOfferPriceFormatAttribute()
    {
        return price($this->offer_price);
    }
    public function getFinalPriceFormatAttribute()
    {
        return price($this->final_price);
    }
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function decreaseProducts()
    {
        $bodies = $this->bodies()->with('product')->get();
        foreach ($bodies as $body) {
            $body->product->editStock(-1*($body->count));
        }
    }

    public function totalPrice()
    {
        $bodies = $this->bodies;
        $total = 0;
        foreach ($bodies as $body) {
            $total += $body->body_price;
        }
        return $total;
    }

    public function crud($g_id = 0, $f_type, $person_id = 0, $products)
    {
        $payment_type = new PaymentType;
        $offers = Offer::all();
        $factor = $this;
        $categories = Category::where('status', 1)->orderBy('order')->get();
        $person_note = false;
        if ($person_id == 0) {
            $person_name = "";
            $person_family = "";
            $person_gender = 0;
            $person_mobile = "";
        } else {
            $person = Person::find($person_id);
            $person_name = $person->name;
            $person_family = $person->family;
            $person_gender = $person->gender;
            $person_mobile = $person->mobile;
            $person_note = $person->getMeta('person-info');
        }
        if ($f_type == "game") {
            $game = Game::find($g_id);
            if (!$game) {
                return response()->json([
                    'message' => 'متاسفانه خطایی رخ داده است.'
                ], 404);
            }
            $factor = $game->factor ?? $this;
        } else if ($f_type == "nogame") {
            $factor = $this;
        }
?>
        <button type="button" id="close-factor-modal" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo __('فاکتور فروشگاه'); ?></h3>
            </div>
            <?php
            if ($f_type == "nogame") {
            ?>
                <form id="p-search-game">
                    <div class="row">
                        <div class="col-md-10 form-group">
                            <label class="form-label"><?php echo __('انتخاب شخص (نام و نام خانوادگی / موبایل / اشتراک / کد کارت)'); ?></label>
                            <input type="search" id="search-person-input" class="form-control" placeholder="<?php echo __('انتخاب شخص'); ?>...">
                        </div>
                        <div class="col-md-2 form-group d-flex">
                            <button type="submit" class="btn btn-success form-control align-self-end"><?= __('جستجو') ?></button>
                        </div>
                    </div>
                </form>
                <div class="row mb-4">
                    <div class="col-md-10 form-group">
                        <div id="search-result" class="list-group"></div>
                    </div>
                </div>
                <hr>
                <h4><?php echo __('اطلاعات شخص'); ?></h4>
                <div class="row mb-4">
                    <div class="col-md-3 form-group">
                        <label class="form-label"><?php echo __('نام'); ?> <span class="text-danger">*</span></label>
                        <input type="text" id="person-name" data-id="person-name" class="form-control checkEmpty" placeholder="<?php echo __('نام'); ?>..." value="<?php echo $person_name ?>">
                        <div class="invalid-feedback" data-id="person-name" data-error="checkEmpty"></div>
                        <input type="hidden" id="person-id" value="<?php echo $person_id ?>">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="form-label"><?php echo __('نام خانوادگی'); ?> <span class="text-danger">*</span></label>
                        <input type="text" id="person-family" data-id="person-family" class="form-control checkEmpty" placeholder="<?php echo __('نام خانوادگی'); ?>..." value="<?php echo $person_family ?>">
                        <div class="invalid-feedback" data-id="person-family" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="form-label"><?php echo __('جنسیت'); ?><span class="text-danger">*</span></label>
                        <select name="" id="person-gender" data-id="person-gender" class="form-select checkEmpty">
                            <option value="0" value="<?php echo $person_gender == 0 ? "selected" : "" ?>"><?php echo __('دختر'); ?></option>
                            <option value="1" value="<?php echo $person_gender == 1 ? "selected" : "" ?>"><?php echo __('پسر'); ?>v</option>
                        </select>
                        <div class="invalid-feedback" data-id="person-gender" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="form-label"><?php echo __('موبایل'); ?> <span class="text-danger">*</span></label>
                        <input type="text" id="person-mobile" data-id="person-mobile" class="form-control just-numbers checkEmpty checkMobile" placeholder="<?php echo __('موبایل'); ?>..." value="<?php echo $person_mobile ?>" maxlength="11">
                        <div class="invalid-feedback" data-id="person-mobile" data-error="checkEmpty"></div>
                        <div class="invalid-feedback" data-id="person-mobile" data-error="checkMobile"></div>
                    </div>
                </div>
                <hr>

            <?php
            }else{
                ?>
                <p class="text-center">مشتری: <?=$game->person->getFullName()?></p>
                <?php
            }
            ?>
            <?php $factor->showProducts($g_id, $factor->id, $f_type, $products) ?>
        </div>
        <hr>
        <div id="foctor-bodies"><?php $factor->showBodies($f_type) ?></div>
        <?php
        if ($f_type == "nogame" && $factor->bodies->count() > 0) {
        ?>
            <table class="table">
                <tbody>
                    <tr>
                        <th><?php echo __('تخفیف'); ?></th>
                        <td>
                            <select name="offer-type" style="min-width: 130px;" id="offer-type" class="form-select">
                                <option value="1"><?php echo __('مبلغ دلخواه'); ?></option>
                                <option value="2"><?php echo __('کد تخفیف'); ?></option>
                            </select>
                        </td>
                        <td>
                            <select name="offer-code" id="offer-code" data-f_id="<?php echo $factor->id ?>" class="form-select factor-offer-field offer-code-field">
                                <option value="0"><?php echo __('هیچکدام'); ?></option>
                                <?php
                                foreach ($offers as $offer) { ?>
                                    <option value="<?php echo $offer->id ?>" <?php echo $factor->offer_code == $offer->id ? "selected" : "" ?>><?php echo $offer->name ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <input type="text" name="offer-price" id="offer-price" data-id="offer-price" data-f_id="<?php echo $factor->id ?>" class="form-control just-numbers factor-offer-field offer-price-field" placeholder="مبلغ دلخواه..." value="<?php echo $factor->offer_price ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-center">
                            <h4 class="m-0"><?php echo __('مبلغ قابل پرداخت'); ?>:
                                <?php echo cnf($factor->final_price) ?>
                            </h4>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php $payment_type->loadPaymentTypes($factor->final_price); ?>
            <div class="row mb-3">
                <div class="text-center cnt_btns_bill col-md-12">
                    <button class="btn btn-danger close-factor" data-f_id=<?php echo $factor->id ?>>
                        <i class="bx bx-check"></i>
                        <?php echo __('بستن صورتحساب'); ?>
                    </button>
                </div>
            </div>
        <?php
        }
    }

    public function showProducts($g_id = 0, $f_id = 0, $f_type, $products, $name = "", $category_name = "")
    {
        $products = $products->where('status', 1);
        $categories = Category::where('status', 1)->orderBy('order')->get();
        $setting = new Setting;
        $show_stock = $setting->getSetting('show_product_stock') ?? 0;
        $just_instock = $setting->getSetting('just_in_stock_product_status') ?? 0;
        if ($just_instock) {
            $products = $products->where('stock', '>', 0);
        }
        ?>
        <div id="factor-products">
            <?php
            if ($g_id > 0) {
                $game = Game::find($g_id);
                if ($game->person->getMeta('person-info') != "") { ?>
                    <div class="alert alert-danger text-center"><?php echo $game->person->getMeta('person-info'); ?></div>
            <?php
                }
            }
            ?>
            <div class="row mb-4">
                <h5><?php echo __('جستجو'); ?></h5>
                <div class="col-md-4 form-group">
                    <label class="form-label"><?php echo __('نام محصول'); ?></label>
                    <input type="text" class="form-control" id="f-product-name" placeholder="<?php echo __('نام محصول'); ?>..." value="<?php echo $name; ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label"><?php echo __('دسته'); ?></label>
                    <select id="f-product-category" class="form-select select2-f">
                        <option value=""><?php echo __('همه'); ?></option>
                        <?php
                        foreach ($categories as $category) { ?>
                            <option <?php echo $category_name == $category->id ? "selected" : ""; ?> value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                        <?php
                        } ?>
                    </select>
                </div>
                <div class="col-md-4 form-group d-flex align-items-end justify-content-start">
                    <button class="btn btn-success" id="f-product-search" data-f_id="<?php echo $f_id; ?> " data-f_type="<?php echo $f_type; ?>" data-g_id="<?php echo $g_id ?>"><?php echo __('جستجو'); ?></button>
                </div>
            </div>
            <hr>
            <div id="pesrson-note"> </div>
            <?php
            if ($products->count() > 0) {
                foreach ($products as $product) {
                    $image = $product->image;
            ?>
                    <div class="d-inline-flex flex-column justify-content-center btn btn-<?php echo $product->stock > 0 ? "light" : "danger" ?> mb-3 mx-1 store-body" data-f_id="<?php echo $f_id ?? 0 ?>" data-f_type="<?php echo $f_type ?>" data-g_id="<?php echo $g_id ?>" data-p_id="<?php echo $product->id ?>">
                        <div class="form-image-container mb-1 d-inline-flex justify-content-center <?php echo $image == '' ? 'd-none' : '' ?>">
                            <img src="<?php echo $image ?>" class="form-image">
                        </div>
                        <div>
                            <?php echo $product->name ?> <?php echo $show_stock == 1 ? '(' . $product->stock . ')' : '' ?>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="alert alert-danger text-center m-1"><?php echo __('محصولی یافت نشد.'); ?></div>
            <?php
            }
            ?>
        </div>
        <?php
    }

    public function showBodies($f_type)
    {
        $bodies = $this->bodies;
        if ($bodies->count() > 0) {
        ?>
            <div class="table-responsive">
                <table class="table shop-table table-bordered table-hover">
                    <thead class="table-warning">
                        <tr>
                            <th class="text-center"><?php echo __('#'); ?></th>
                            <th class="text-center"><?php echo __('محصول'); ?></th>
                            <th class="text-center"><?php echo __('قیمت واحد'); ?></th>
                            <th class="text-center"><?php echo __('تعداد'); ?></th>
                            <th class="text-center"><?php echo __('کل'); ?></th>
                            <th class="no-print text-center"><?php echo __('حذف'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($bodies as $body) { ?>
                            <tr>
                                <td class="text-center"><?php echo $i; ?></td>
                                <td class="text-center"><?php echo $body->product_name; ?></td>
                                <td class="text-center">
                                    <input type="text" style="min-width: 90px;" data-id="p-price<?php echo $i; ?>" class="form-control no-print money-filter" value="<?php echo cnf($body->product_price); ?>">
                                    <input type="hidden" name="p-price" id="p-price<?php echo $i; ?>" class="no-print update-factor just-numbers" value="<?php echo $body->product_price; ?>" data-game=<?= $this->game_id ?? 0 ?> data-id="<?php echo $body->id ?>" data-f_type="<?php echo $f_type ?>">
                                    <span class="just-print"><?php echo $body->product_price; ?></span>
                                </td>
                                <td class="text-center">
                                    <input type="number" style="min-width: 60px;" name="count" class="form-control no-print update-factor" value="<?php echo $body->count; ?>" data-id="<?php echo $body->id ?>" data-game="<?= $this->game_id ?>" data-f_type="<?php echo $f_type ?>">
                                    <span class="just-print"><?php echo $body->count; ?></span>
                                </td>
                                <td class="text-center"><?php echo cnf($body->body_price); ?></td>
                                <td class="no-print text-center">
                                    <button type="button" class="btn btn-danger btn-sm delete-body" data-id="<?php echo $body->id ?>" data-game="<?= $this->game_id ?>" data-f_type="<?php echo $f_type ?>"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                        <?php
                            $i++;
                        }
                        ?>
                        <tr>
                            <td class="text-center" colspan="100%">
                                <h4 class="m-0"><?php echo __('مجموع فروشگاه'); ?>: <?php echo cnf($this->total_price) ?></h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php
        }
    }

    public function showIndex($factors)
    {
        if ($factors->count() > 0) {
        ?>
            <table class="table table-bordered">
                <tr>
                    <td><?php echo __('ردیف'); ?></td>
                    <td><?php echo __('نام شخص'); ?></td>
                    <td><?php echo __('تخفیف'); ?></td>
                    <td><?php echo __('مبلغ بعد از تخفیف'); ?></td>
                    <td><?php echo __('نوع'); ?></td>
                    <td><?php echo __('تاریخ'); ?></td>
                </tr>
                <?php
                foreach ($factors as $factor) { ?>
                    <tr data-bs-toggle="modal" data-bs-target="#bodies-modal" style="cursor: pointer;" data-f_id="<?php echo $factor->id ?>" class="crud-bodies">
                        <td><?php echo $factor->id ?></td>
                        <td><?php echo $factor->person_fullname ?></td>
                        <td><?php echo $factor->offer_price ?></td>
                        <td><?php echo $factor->final_price ?></td>
                        <td><?php echo $factor->game ? "بازی" : "آزاد" ?></td>
                        <td><?php echo dateFormat($factor->created_at) ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        <?php
        } else {
        ?>
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?php echo __('موردی جهت نمایش موجود نیست.'); ?></div>
                </div>
            </div>
<?php
        }
    }
}
