<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Offer extends Main
{
    use HasFactory, SoftDeletes, Syncable;

    protected $fillable = ['id', 'name', 'type', 'per', 'min_price', 'calc', 'details', 'times_used'];

    public function showIndex()
    {
        $offers = Offer::orderBy('name', 'desc')->get();
        if ($offers->count() > 0) {
?>
            <table id="offer-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام کد'); ?></th>
                        <th><?php echo __('نوع تخفیف'); ?></th>
                        <th><?php echo __('مقدار تخفیف'); ?></th>
                        <th><?php echo __('حداقل مبلغ'); ?></th>
                        <th><?php echo __('توضیحات'); ?></th>
                        <th><?php echo __('مدیریت'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($offers as $offer) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $offer->name; ?></td>
                            <td><?php echo $offer->type; ?></td>
                            <td><?php echo cnf($offer->per); ?></td>
                            <td><?php echo cnf($offer->min_price); ?></td>
                            <td><?php echo $offer->details; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $offer->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-offer" data-id="<?php echo $offer->id; ?>"><i class="bx bx-trash"></i></button>
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
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?php echo __('موردی جهت نمایش موجود نیست.'); ?></div>
                </div>
            </div>
        <?php
        }
    }

    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $title = __('کد تخفیف جدید');
            $name = "";
            $type = "";
            $per = "";
            $min_price = "";
            $calc = "";
            $details = "";
        } else if ($action == "update") {
            $title = __('ویرایش کد تخفیف');
            $offer = Offer::find($id);
            $name = $offer->name;
            $type = $offer->type;
            $per = $offer->per;
            $min_price = $offer->min_price;
            $calc = $offer->calc;
            $details = $offer->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نام کد'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام کد'); ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نوع تخفیف'); ?> <span class="text-danger">*</span></label>
                    <select name="type" id="type" data-id="type" class="form-select checkEmpty">
                        <option value="درصد" <?php echo $type == "درصد" ? 'selected' : '' ?>><?php echo __('درصد'); ?></option>
                        <option value="مبلغ" <?php echo $type == "مبلغ" ? 'selected' : '' ?>><?php echo __('مبلغ'); ?></option>
                    </select>
                    <div class="invalid-feedback" data-id="type" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('مقدار تخفیف'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="per" data-id="per" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('مقدار تخفیف'); ?>..." value="<?php echo ($per != "" ? cnf($per) : ""); ?>" onkeypress="return onlyNumberKey(event)">
                    <input type="hidden" name="per" id="per" data-id="per" class="form-control just-numbers checkEmpty" value="<?php echo $per ?>">
                    <div class="invalid-feedback" data-id="per" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('حداقل مبلغ'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="min_price" data-id="min-price" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('حداقل مبلغ'); ?>..." value="<?php echo ($min_price != "" ? cnf($min_price) : ""); ?>" onkeypress="return onlyNumberKey(event)">
                    <input type="hidden" name="min_price" id="min-price" data-id="min-price" class="form-control just-numbers checkEmpty" placeholder="<?php echo __('حداقل مبلغ'); ?>..." value="<?php echo $min_price ?>">
                    <div class="invalid-feedback" data-id="min-price" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?php echo __('نحوه اعمال تخفیف'); ?></label>
                    <select name="calc" id="calc" class="form-select">
                        <option value="all" <?php echo $calc == "all" ? "selected" : "" ?>><?php echo __('کل صورتحساب'); ?></option>
                        <option value="game" <?php echo $calc == "game" ? "selected" : "" ?>><?php echo __('بازی'); ?></option>
                        <option value="factor" <?php echo $calc == "factor" ? "selected" : "" ?>><?php echo __('فروشگاه'); ?></option>
                    </select>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?php echo __('توضیحات'); ?></label>
                    <input type="text" name="details" id="details" class="form-control" placeholder="<?php echo __('توضیحات'); ?>..." value="<?php echo $details; ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-offer" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات'); ?></button>
                    <?php
                    } else if ($action == "update") {
                    ?>
                        <button type="button" id="store-offer" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات'); ?></button>
                    <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف'); ?></button>
                </div>
            </div>
        </div>
    <?php
    }

    public function crudGame($g_id)
    {
        $game = Game::find($g_id);
        if (!$game) {
            return response()->json([
                'message' => 'متاسفانه خطایی رخ داده است.'
            ], 404);
        }
        $offer_code = $game->offer_code;
        $offer_price = $game->offer_price;
        $offer_calc = $game->offer_calc;
        $offers = Offer::all();
    ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo __('تخفیف'); ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نوع تخفیف'); ?> <span class="text-danger">*</span></label>
                    <select name="offer-type" id="offer-type" class="form-select">
                        <option value="1" <?php echo $game->offer_code == 0 ? "selected" : "" ?>><?php echo __('مبلغ دلخواه'); ?></option>
                        <option value="2" <?php echo $game->offer_price == 0 ? "selected" : "" ?>><?php echo __('کد تخفیف'); ?></option>
                    </select>
                </div>
                <div class="col-md-6 form-group offer-code-field">
                    <label class="form-label required"><?php echo __('کد تخفیف'); ?> <span class="text-danger">*</span></label>
                    <select name="offer-code" id="offer-code" class="form-select">
                        <option value="0"><?php echo __('هیچکدام'); ?></option>
                        <?php
                        foreach ($offers as $offer) {
                        ?>
                            <option value="<?php echo $offer->id ?>" <?php echo $offer->id == $offer_code ? "selected" : "" ?>><?php echo $offer->name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 form-group offer-price-field">
                    <label class="form-label"><?php echo __('مبلغ دلخواه'); ?></label>
                    <input type="text" name="offer-price" id="offer-price" data-id="offer-price" class="form-control just-numbers" placeholder="<?php echo __('مبلغ دلخواه'); ?>..." value="<?php echo $offer_price ?>">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group offer-price-field">
                    <label class="form-label required"><?php echo __('نحوه اعمال تخفیف'); ?></label>
                    <select name="offer-calc" id="offer-calc" class="form-select">
                        <option value="all" <?php echo $offer_calc == "all" ? "selected" : "" ?>><?php echo __('کل صورتحساب'); ?></option>
                        <option value="game" <?php echo $offer_calc == "game" ? "selected" : "" ?>><?php echo __('بازی'); ?></option>
                        <option value="factor" <?php echo $offer_calc == "factor" ? "selected" : "" ?>><?php echo __('فروشگاه'); ?></option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button type="button" id="store-offer" data-g_id="<?php echo $g_id ?>" class="btn btn-success me-sm-3 me-1"><?php echo __('ثبت اطلاعات'); ?></button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف'); ?></button>
                </div>
            </div>
        </div>
<?php
    }
}
