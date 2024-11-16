<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'name', 'price', 'expire_time', 'expire_day','type'];

    public function showIndex()
    {
        $packages = Package::orderBy('name', 'desc')->get();
        if($packages->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                <tr>
                    <th><?php echo __('ردیف') ?></th>
                    <th><?php echo __('نام') ?></th>
                    <th><?php echo __('هزینه') ?></th>
                    <th><?php echo __('شارژ به دقیقه') ?></th>
                    <th><?php echo __('شارژ به روز') ?></th>
                    <th><?php echo __('مدیریت') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($packages as $package) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $package->name; ?></td>
                        <td><?php echo cnf($package->price); ?></td>
                        <td><?php echo cnf($package->expire_time); ?></td>
                        <td><?php echo cnf($package->expire_day);?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $package->id; ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-package" data-id="<?php echo $package->id; ?>"><i class="bx bx-trash"></i></button>
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
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?php echo __('موردی جهت نمایش موجود نیست.') ?></div>
                </div>
            </div>
            <?php
        }
    }

    public function crud($action, $id = 0)
    {
        return view('package.crud-modal',compact('action','id'));
        if ($action == "create") {
            $title = "بسته جدید";
            $name = "";
            $price = "";
            $expire_time = "";
            $expire_day = "";
        } else if ($action == "update") {
            $title = "ویرایش بسته";
            $package = Package::find($id);
            $name = $package->name;
            $price = $package->price;
            $expire_time = $package->expire_time;
            $expire_day = $package->expire_day;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نام بسته ') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام بسته ') ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('هزینه') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="price" data-id="price" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('هزینه') ?>..." value="<?php echo ($price != "" ? cnf($price) : ""); ?>">
                    <input type="hidden" name="price" id="price" data-id="price" class="form-control just-numbers checkEmpty" value="<?php echo $price; ?>">
                    <div class="invalid-feedback" data-id="price" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('شارژ به دقیقه') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="expire_time" data-id="expire-time" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('شارژ به دقیقه') ?>..." value="<?php echo ($expire_time != "" ? cnf($expire_time) : ""); ?>">
                    <input type="hidden" name="expire_time" id="expire-time" data-id="expire-time" class="form-control just-numbers checkEmpty" value="<?php echo $expire_time; ?>">
                    <div class="invalid-feedback" data-id="expire-time" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('شارژ به روز') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="expire_day" data-id="expire-day" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('شارژ به روز') ?>..." value="<?php echo ($expire_day != "" ? cnf($expire_day) : ""); ?>">
                    <input type="hidden" name="expire_day" id="expire-day" data-id="expire-day" class="form-control just-numbers checkEmpty" value="<?php echo $expire_day; ?>">
                    <div class="invalid-feedback" data-id="expire-day" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-package" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                        <?php
                    } else {
                        ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <button type="button" id="store-package" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                        <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
                </div>
            </div>
        </div>
        <?php
    }

    public function chargeList($records)
    {
        if($records->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام شخص') ?></th>
                        <th><?php echo __('نام بسته') ?></th>
                        <th><?php echo __('شارژ باقیمانده (دقیقه)') ?></th>
                        <th><?php echo __('تاریخ انقضاء') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($records as $key => $record) { ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $record->getFullName() ?></td>
                        <td><?php echo $record->package?->name ?></td>
                        <td><?php echo cnf($record->sharj) ?></td>
                        <td><?php echo dateFormat($record->expire) ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $record->id ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-charge" data-id="<?php echo $record->id ?>">
                                <i class="bx bx-trash"></i>
                            </button>
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
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?php echo __('موردی جهت نمایش موجود نیست.') ?></div>
                </div>
            </div>
            <?php
        }
    }

    public function crudCharge($id, $p_id, $action)
    {
        if ($action == "create") {
            $packages = Package::all();
            $payment_type = new PaymentType;
            $title =  __('شارژ جدید');
            $wallet = new Wallet;
            if ($id == null || $id == 0) {
                $person = new Person;
            } else {
                $person = Person::find($id);
            }
            if ($p_id == null || $p_id == 0) {
                $package = new Package;
                $price = null;
            } else {
                $package = Package::find($p_id);
                $price = $package->price;
            }
            ?>
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <form id="store-charge-form">
                    <div class="row mb-4">
                        <div class="col-md-6 form-group">
                            <label class="form-label"><?php echo __('مشتری') ?> <span class="text-danger">*</span></label>
                            <select name="person" id="person" data-id="person" class="form-select searchPersonL checkEmpty charge-field">

                            </select>
                            <div class="invalid-feedback" data-id="person" data-error="checkEmpty"></div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label"><?php echo __('بسته') ?> <span class="text-danger">*</span></label>
                            <select name="package" id="package" data-id="package" class="form-select select2 checkEmpty charge-field">
                                <option value=""><?php echo __('انتخاب کنید') ?></option>
                                <?php foreach ($packages as $p) { ?>
                                    <option value="<?php echo $p->id ?>" data-price="<?php echo cnf($p->price) ?>" <?php echo $package->id == $p->id ? "selected" : "" ?>><?php echo $p->name ?></option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback" data-id="package" data-error="checkEmpty"></div>
                        </div>
                    </div>
                    <?php
                    if ($price != null) { ?>
                        <div class="row-mb-4" id="display-total-price">
                            <div class="col-12 text-center">
                                <h5><?php __('مبلغ قابل پرداخت') ?>: <span id="total-price"><?php echo cnf($price) ?></span></h5>
                            </div>
                        </div>
                        <?php
                    } ?>

                    <?php
                    $payment_type->loadPaymentTypes($price, $id, true);
                    ?>

                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="button" id="store-charge" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 store-charge submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        } else if ($action == "update") {
            $title = __("ویرایش شارژ");
            $person = Person::find($id);
            $sharj = $person->sharj;
            $e = \Carbon\Carbon::create($person->expire);
            $expire = $e->diffInDays(today());
            ?>
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 form-group">
                        <label class="form-label required"><?= $person->sharj_type=='times'?__('شارژ (مرتبه)'): __('شارژ (دقیقه)') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="sharj" data-id="sharj" class="form-control money-filter just-numbers" placeholder="<?php echo __('شارژ (دقیقه)') ?>..." value="<?php echo ($sharj != "" ? cnf($sharj) : ""); ?>">
                        <input type="hidden" name="sharj" id="sharj" class="form-control just-numbers" value="<?php echo $sharj; ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label required"><?php echo __('روز باقیمانده') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="expire" data-id="expire" class="form-control money-filter just-numbers" placeholder="<?php echo __('روز باقیمانده') ?>..." value="<?php echo ($expire != "" ? cnf($expire) : ""); ?>">
                        <input type="hidden" name="expire" id="expire" class="form-control just-numbers" value="<?php echo $expire; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <button type="button" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 store-charge submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}
