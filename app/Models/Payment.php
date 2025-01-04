<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','uuid', 'person_id', 'price', 'details', 'type', 'object_type', 'object_id'];
    protected $appends = ['person_name'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
    public $types = [
        "game" => "بازی",
        "factor" => "فاکتور فروشگاه",
        "offer" => "تخفیف",
        "charge_wallet" => "شارژ کیف پول",
        "wallet" => "کیف پول",
        "charge_package" => "شارژ بسته",
        "remove_debt" => "تسویه حساب",
        "course" => "کلاس",
        "vat" => 'مالیات',
        'rounded' => 'رند شده',
        'deposit' => 'پیش پرداخت'
    ];


    public function person()
    {
        return $this->belongsTo(Person::class);
    }
    public function getPersonNameAttribute()
    {
        return $this->person?->name . ' ' . $this->person?->family;
    }
    public function getSubmitDateAttribute()
    {
        return persianTime($this->created_at);
    }
    public function getTypeValueAttribute()
    {
        $payment_type = PaymentType::where("name", $this->type)->first();
        return $payment_type->label ?? $this->types[$this->type] ?? $this->type;
    }

    public function getPriceFormatAttribute()
    {
        return price($this->price);
    }

    public function type()
    {
        return $this->type_value;
    }

    public function getPriceBadge()
    {
        if ($this->price > 0) {
            return '<span class="badge bg-success">' . cnf($this->price) . '</span>';
        } else if ($this->price < 0) {
            return '<span class="badge bg-danger">' . cnf($this->price) . '</span>';
        } else if ($this->price == 0) {
            return '<span class="badge bg-warning">' . cnf($this->price) . '</span>';
        }
    }

    public function object()
    {
        return $this->morphTo();
    }

    public function showIndex($payments)
    {
        if ($payments->count() > 0) {
?>
            <table class="table table-bordered">
                <tr>
                    <td>ردیف</td>
                    <td>نام شخص</td>
                    <td><?=__('مبلغ')?></td>
                    <td>نوع پرداخت</td>
                    <td>تاریخ</td>
                    <td><?=__('توضیحات')?></td>
                    <td>مدیریت</td>
                </tr>
                <?php
                foreach ($payments as $payment) {
                ?>
                    <tr>
                        <td><?php echo $payment->id ?></td>
                        <td><?php echo $payment->person?->getFullName() ?></td>
                        <td><?php echo $payment->getPriceBadge() ?></td>
                        <td><?php echo $payment->type() ?></td>
                        <td><?php echo dateFormat($payment->created_at) ?></td>
                        <td><?php echo $payment->details ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $payment->id; ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                        </td>
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
                    <div class="alert alert-danger text-center m-0">موردی جهت نمایش موجود نیست.</div>
                </div>
            </div>
        <?php
        }
    }

    public function crud($action, $id = 0, $debt = false)
    {
        // $people = Person::all();
        $payment_type = new PaymentType;
        $payment = null;
        if ($action == "create") {
            $title = __("پرداخت جدید");
            $person_id = 0;
        } else if ($action == "update") {
            $title = __("ویرایش پرداخت");
            $payment = Payment::find($id);
            $person_id = $payment->person_id;
        } else if ($action == 'remove-debt') {
            if ($debt['person_id']!=0) {
                # code...
                $title = __("تسویه حساب-") . $debt['person_name'];
                $person_id = $debt['person_id'];
                $payment = abs($debt['price']);
             }
             //else{
            //     person::query()->update([
            //         'balance'=>0
            //     ]);
            //     return true;
            // }
            // dd($title,$person_id);
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo __($title); ?></h3>
            </div>
            <form id="store-payment-form">
                <?php if ($action == 'update' or $action == 'remove-debt') { ?>
                    <input type="hidden" id="person-id" value="<?php echo $person_id ?>">
                <?php  } else { ?>
                    <div class="row mb-4">
                        <div class="col-md-12 form-group">
                            <label class="form-label required"><?=__('انتخاب شخص')?> <span class="text-danger">*</span></label>
                            <select name="" id="person-id" data-id="person-id" class="form-select searchPersonM checkEmpty">
                            </select>
                            <div class="invalid-feedback" data-id="person-id" data-error="checkEmpty"></div>
                        </div>
                    </div>
                <?php }
                if ($action == 'update') {
                    $payment_type->updatePrice($payment);
                } else {
                    $payment_type->loadPaymentTypes($payment, 0, false, $debt);
                }
                ?>

                <div class="row">
                    <div class="col-12 text-center">
                        <?php
                        if ($action == "create" or $action == 'remove-debt') { ?>
                            <button type="button" id="store-payment" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <?php
                        } else if ($action == "update") { ?>
                            <button type="button" id="store-payment" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                        <?php
                        }  ?>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
        </div>
<?php
    }

    public static function storeAll(array $prices, array $details, Person $person, string $object_type = null, int $object_id = null, string $description = null): array
    {
        $balance = 0;
        $clubable = 0;
        foreach ($prices as $key => $price) {
            if ($price != "" && $price != 0) {
                $d = $details[$key] ?? null;
                if ($key == "wallet") {
                    $wallet_value = $person->wallet_value - $price;
                    Wallet::create([
                        'person_id' => $person->id,
                        'balance' => $wallet_value,
                        'price' => $price * (-1),
                        'gift_percent' => 0,
                        'final_price' => $price * (-1),
                        'description' => $description
                    ]);
                    $person->wallet_value = $wallet_value;
                    $person->save();
                }
                Payment::create([
                    "user_id" => auth()->id(),
                    "person_id" => $person->id,
                    "price" => $price,
                    "details" => $d ? $d : $description,
                    "type" => $key,
                    "object_type" => $object_type,
                    "object_id" => $object_id,

                ]);
                $balance += $price;

                $payment_type = PaymentType::where('name', $key)->first();
                if ($payment_type?->club == 1) {
                    $clubable += $price;
                }
            }
        }
        return [
            'balance' => $balance,
            'clubable' => $clubable
        ];
    }
}
