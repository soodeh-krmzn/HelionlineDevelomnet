<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Wallet extends Main
{
    use HasFactory, SoftDeletes, Syncable;

    protected $fillable = ['person_id', 'balance', 'price', 'gift_percent', 'final_price', 'description', 'expire'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function showIndex($wallets)
    {
        if ($wallets->count() > 0) {
?>
            <table class="table table-bordered">
                <tr>
                    <td>ردیف</td>
                    <td>نام شخص</td>
                    <td>موجودی</td>
                    <td><?= __('مبلغ') ?></td>
                    <td>ضریب هدیه</td>
                    <td>مبلغ نهایی</td>
                    <td>تاریخ</td>
                    <td><?= __('توضیحات') ?></td>
                </tr>
                <?php
                $i = 1;
                foreach ($wallets as $wallet) { ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $wallet->person?->getFullName() ?></td>
                        <td><?php echo cnf($wallet->balance) ?></td>
                        <td><?php echo cnf($wallet->price) ?></td>
                        <td><?php echo $wallet->gift_percent . " %" ?></td>
                        <td><?php echo cnf($wallet->final_price) ?></td>
                        <td><?php echo dateFormat($wallet->created_at) ?></td>
                        <td><?php echo $wallet->description ?></td>
                    </tr>
                <?php
                    $i++;
                }
                ?>
            </table>
        <?php
        } else {
        ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?= __('موردی جهت نمایش موجود نیست.') ?></div>
                </div>
            </div>
        <?php
        }
    }

    public function crud($action, $id = 0)
    {
        // $people = Person::all();
        $payment_type = new PaymentType;
        $setting = new Setting;
        $gift_percentage = $setting->getSetting("wallet_gift_percent") . " %" ?? "0 %";
        $title =  __('شارژ جدید');
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <form id="store-wallet-form">
                <div class="row mb-4">
                    <div class="col-md-6 form-group">
                        <label class="form-label required"><?= __('انتخاب شخص') ?> <span class="text-danger">*</span></label>
                        <select name="" id="person-id" data-id="person-id" class="form-select searchPersonM checkEmpty">
                        </select>
                        <div class="invalid-feedback" data-id="person-id" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label required"><?= __('مبلغ') ?> <span class="text-danger">*</span></label>
                        <input type="text" data-id="price" id="price-input" class="form-control just-numbers checkEmpty money-filter" placeholder="مبلغ...">
                        <input type="hidden" name="price" id="price" data-id="price" class="form-control checkEmpty just-numbers">
                        <div class="invalid-feedback" data-id="price" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 form-group">
                        <label class="form-label required"><?= __('توضیحات') ?></label>
                        <input type="text" name="description" id="description" class="form-control" placeholder="<?= __('توضیحات') ?>...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?= __('احتساب شارژ هدیه') ?> (<?php echo $gift_percentage ?>)</label>
                        <div class="w-100">
                            <label class="switch switch-success switch-lg align-self-end">
                                <input type="checkbox" class="switch-input" id="gift">
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"><?= __('بله') ?></span>
                                    <span class="switch-off"><?= __('خیر') ?></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <?php $payment_type->loadPaymentTypes(); ?>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" id="store-wallet" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?= __('ثبت اطلاعات') ?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?= __('انصراف') ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function loadWallet($person_id, &$price)
    {
        $setting = new Setting();
        $walletAsDefaultPay = $setting->getSetting('walletAsDefaultPay');
        $walletAsDefaultPay =($walletAsDefaultPay!='false' and $walletAsDefaultPay!="" )?true :false;

        $person = Person::find($person_id);
        if ($person) {
            $walletinput=0;
            if ($walletAsDefaultPay) {
                if ($person->wallet_value < $price) {
                    $price = $price - $person->wallet_value;
                    $walletinput = $person->wallet_value;
                } else {
                    $walletinput = $price;
                    $price = 0;
                }
            }
        ?>

            <tr>
                <th>
                    <button type='button' class="btn btn-success btn-sm p-1 me-1 enter-default" data-target="wallet-pay">
                        <i class="bx bx-left-arrow-circle"></i>
                    </button>
                    <?= __('کیف پول') ?> (<?= __('مبلغ') ?>: <?php echo cnf($person->wallet_value ?? 0) ?>)
                </th>
                <td>
                    <input type="text" data-id="wallet-pay" value="<?= cnf($walletinput) ?>" class=" form-control just-numbers money-filter" max="<?php echo $person->wallet_value ?? 0 ?>" placeholder="مبلغ کیف پول...">
                    <input type="hidden" name="wallet" value="<?= $walletinput ?>" id="wallet-pay" class=" form-control just-numbers payment-price" max="<?php echo $person->wallet_value ?? 0 ?>">
                </td>
                <td>
                    <input type="text" name="wallet" id="wallet-details" class="form-control payment-details" placeholder="توضیحات...">
                </td>
            </tr>
<?php
        }
    }
}
