<?php

namespace App\Models;

use DateTime;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\MyModels\Main;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PaymentController;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentType extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'label', 'details', 'status', 'club'];

    public function status()
    {
        if ($this->status == 0) {
            return '<span class="badge bg-danger">'.__('غیرفعال').'</span>';
        } else {
            return '<span class="badge bg-success">'.__('فعال').'</span>';
        }
    }

    public function club()
    {
        if ($this->club == 0) {
            return '<span class="badge bg-danger">'.__('خیر').'</span>';
        } else {
            return '<span class="badge bg-success">'.__('بله').'</span>';
        }
    }

    public function showPerPaymentType($data)
    {
        $request = new Request();
        $request = $request->merge($data);
        // dump('here');

        $paymentController = new PaymentController();
        $data = [];
        // dd($payments->where('type','Cash')->sum('price'));
        // dd(clone$payments,$payments->clone()->get(),$payments->get());
        // foreach ($this->all() as $item) {
        //     $payments = $paymentController->search($request);
        //     $sum = $payments->where('type', $item->name)->sum('price');
        //     $data["$item->label"] = $sum;
        // }
        $payments = $paymentController->search($request);

        $payments->whereNotIn('type', [
            "game",
            "factor",
            "offer",
            "charge_wallet",
            "charge_package",
            "remove_debt",
            "course",
            "vat",
            'rounded',
            'deposit'
        ])->select('type',DB::raw("SUM(price) as price"))->groupBy('type');
        $payments=$payments->get();
        //  dd($data,$request->all());
        return view('report.sections.showPerPaymentType', compact('payments'));
    }

    public function showIndex()
    {
        $paymentTypes = PaymentType::orderBy('name', 'desc')->get();
        if ($paymentTypes->count() > 0) {
?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نام</th>
                        <th>برچسب</th>
                        <th><?=__('توضیحات')?></th>
                        <th><?=__('وضعیت')?></th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($paymentTypes as $paymentType) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $paymentType->name; ?></td>
                            <td><?php echo $paymentType->label; ?></td>
                            <td><?php echo $paymentType->details; ?></td>
                            <td><?php echo $paymentType->status; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $paymentType->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-payment-type" data-id="<?php echo $paymentType->id; ?>"><i class="bx bx-trash"></i></button>
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
                    <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
        <?php
        }
    }

    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $title = __("روش پرداخت جدید");
            $name = "";
            $label = "";
            $details = "";
            $status = "";
            $club = "";
        } else if ($action == "update") {
            $title = __("ویرایش روش پرداخت");
            $paymentType = PaymentType::find($id);
            $name = $paymentType->name;
            $label = $paymentType->label;
            $details = $paymentType->details;
            $status = $paymentType->status;
            $club = $paymentType->club;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('نام')?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('نام')?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('برچسب')?> <span class="text-danger">*</span></label>
                    <input type="text" name="label" id="label" data-id="label" class="form-control checkEmpty" placeholder="<?=__('برچسب')?>..." value="<?php echo $label; ?>">
                    <div class="invalid-feedback" data-id="label" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('وضعیت')?><span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" <?php echo $status == 1 ? "selected" : "" ?>><?=__('فعال')?></option>
                        <option value="0" <?php echo $status == 0 ? "selected" : "" ?>><?=__('غیرفعال')?></option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('مشمول امتیاز باشگاه')?> <span class="text-danger">*</span></label>
                    <select name="club" id="club" class="form-select">
                        <option value="1" <?php echo $club == 1 ? "selected" : "" ?>><?=__('فعال')?></option>
                        <option value="0" <?php echo $club == 0 ? "selected" : "" ?>><?=__('غیرفعال')?></option>
                    </select>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('توضیحات')?></label>
                    <input type="text" name="details" id="details" class="form-control" placeholder="<?=__('توضیحات')?>..." value="<?php echo $details; ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-payment-type" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                    <?php
                    } else {
                    ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <button type="button" id="store-payment-type" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                    <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                </div>
            </div>
        </div>
    <?php
    }

    public static function getSelect()
    {
        $paymentTypes = PaymentType::where('status', 1)->orderBy('name', 'desc')->get();
        return $paymentTypes;
    }

    public function select()
    {
        $setting = new Setting;
    ?>
        <label class="form-label"><?=__('روش پرداخت پیشفرض')?></label>
        <?php
        if ($this->getSelect()->count() > 0) {
        ?>
            <select name="default_payment_type" class="form-select setting">
                <option value=""><?=__('هیچکدام')?></option>
                <?php
                foreach ($this->getSelect() as $paymentTypeRow) {
                ?>
                    <option <?php echo ($paymentTypeRow->name == $setting->getSetting('default_payment_type')) ? 'selected' : ''; ?> value="<?php echo $paymentTypeRow->name; ?>"><?php echo $paymentTypeRow->label; ?></option>
                <?php
                }
                ?>
            </select>
        <?php
        } else {
        ?>
            <span class="badge bg-danger"><?=__('بدون روش پرداخت')?></span>
        <?php
        }
    }

    public function loadPaymentTypes($payment = null, $person_id = 0, $show_wallet = false, $debt = false)
    {
        $payment_types = PaymentType::where('status', 1)->get();
        $setting = new Setting;
        $default = $setting->getSetting('default_payment_type');
        $wallet = new Wallet;
        $person = Person::find($person_id);
        ?>
        <div class="table-responsive">
            <table id="payment-methods-table" class="payment-methods-table mt-4 table no-print">
                <tbody>
                    <?php
                    if ($show_wallet == true && $person?->wallet_value > 0) {
                        $wallet->loadWallet($person_id);
                    }
                    if ($payment_types->count() > 0) {
                        foreach ($payment_types as $payment_type) {
                    ?>
                            <tr>
                                <th><?php echo $payment_type->label ?></th>
                                <td>
                                    <input type="text" data-id="<?php echo $payment_type->name ?>-pay" class="form-control just-numbers money-filter payment-input" placeholder="<?=__('مبلغ')?> <?php echo $payment_type->label ?>..." value="<?php echo $payment_type->name == $default ? cnf($payment) : '' ?>">
                                    <input type="hidden" name="<?php echo $payment_type->name ?>" id="<?php echo $payment_type->name ?>-pay" class="form-control just-numbers payment-price" value="<?php echo $payment_type->name == $default ? $payment : '' ?>">
                                </td>
                                <td>
                                    <input type="text" name="<?php echo $payment_type->name ?>" value="<?= $debt && $payment_type->name == $default ? __('messages.payment_default_description',['person_id'=>$debt['person_id'],'person_name'=>$debt['person_name']]) : '' ?>" id="<?php echo $payment_type->name ?>-details" class="form-control payment-details" placeholder="<?=__('توضیحات')?>...">
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <div class="alert alert-danger text-center m-1"><?= __('هیچ روش پرداختی تعریف نشده است') ?>.</div>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <th colspan="5">
                            <?php echo __('جمع پرداختی'); ?>:
                            <span id="total-pay"><?php echo $default != null ? cnf($payment) : "" ?></span>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php
    }
    public function updatePrice($payment)
    {
        // $wallet = new Wallet;
        // $person = Person::find($person_id);
        $payment_types = PaymentType::where('status', 1)->get();
    ?>
        <div class="table-responsive">
            <table id="payment-methods-table" class="payment-methods-table mt-4 table no-print">
                <tbody>
                    <?php
                    // if ($show_wallet == true && $person?->wallet_value > 0) {
                    //     $wallet->loadWallet($person_id);
                    // }
                    ?>
                    <tr>
                        <th><?=__('مبلغ')?></th>
                        <td>
                            <input type="text" data-id="edit-pay" class="form-control just-numbers money-filter payment-input" placeholder="<?=__('مبلغ')?>..." value="<?= cnf($payment?->price) ?>">
                            <input type="hidden" name="price" id="edit-pay" class="form-control just-numbers payment-price" value="<?php echo $payment?->price ?>">
                        </td>
                        <td>
                            <input type="text" name="details" value="<?= $payment->details ?>" class="form-control payment-details" placeholder="<?=__('توضیحات')?>...">
                        </td>
                    </tr>
                    <tr>
                        <th><?=__('نوع سند')?></th>
                        <td colspan="5">
                            <select id="doctype" class="form-select">
                                <option <?= $payment?->price >= 0 ? 'selected' : '' ?> value="+"><?= __('سند مثبت') ?></option>
                                <option <?= $payment?->price < 0 ? "selected" : '' ?> value="-"><?= __('سند منفی') ?></option>
                            </select>
                        </td>
                    </tr>
                    <?php
                    if ($payment_types->count() > 0) {
                        if (!in_array($payment->type, ['offer', 'rounded', 'vat', 'game', 'factor'])) {

                    ?>

                            <tr>
                                <th>
                                    <label for="" class="mb"> <?=__('روش پرداخت')?></label>
                                </th>
                                <th colspan="5">
                                    <select id="paymentTypeSelect" class="form-select">
                                        <option value=""><?=__('انتخاب کنید')?>...</option>
                                        <?php
                                        foreach ($payment_types as $payment_type) { ?>
                                            <option <?= $payment?->type == $payment_type->name ? 'selected' : '' ?> value="<?= $payment_type->name ?>"><?= $payment_type->name ?></option>
                                        <?php } ?>
                                    </select>
                                </th>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <div class="alert alert-danger text-center m-1"><?= __('هیچ روش پرداختی تعریف نشده است') ?>.</div>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="5">
                            <?=__('جمع پرداختی')?>:
                            <span id="total-pay"><?= cnf($payment?->price) ?></span>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
<?php
    }

    public function getMonthPaymentsList($from_date, $to_date)
    {
        $arr = array();
        $days = (new DateTime($from_date))->diff(new DateTime($to_date))->format('%a');
        $new_from_date = $from_date->copy();
        for ($day = 1; $day <= $days; $day++) {
            $total = Payment::where('type', $this->name)->where('price', '>', 0)
                ->whereDate('created_at', $new_from_date)
                ->sum('price');
            array_push($arr, $total);
            $new_from_date->addDay();
        }
        return $arr;
    }
}
