<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Syncable;
use Illuminate\Support\Str;

class EditReport extends Main
{
    use HasFactory;
    use Syncable;

    protected $fillable = ["user_id", "edited_type", "edited_id", "details"];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
    
    public $models = [
        'charge' => 'App\Models\Person',
        'game' => 'App\Models\Game',
        'loan' => 'App\Models\BookLoan',
        'offer' => 'App\Models\Offer',
        'package' => 'App\Models\Package',
        'product' => 'App\Models\Product',
        'payment'=>'App\Models\Payment'
    ];

    public function getEditedName()
    {
        switch ($this->edited_type) {
            case 'App\Models\Person':
                return $this->edited?->getFullName();
            case 'App\Models\Payment':
                // dd($this->edited->person->getFullName());
                return $this->edited?->person?->getFullName();
            case 'App\Models\Game':
                return $this->edited?->person_fullname;
            case 'App\Models\BookLoan':
                return $this->edited?->book?->name;
            default:
                return $this->edited?->name;
        }
    }

    public function getEditedNameLabel($type)
    {
        switch ($type) {
            case 'App\Models\Person':
                return __('نام شخص');
            case 'App\Models\Game':
                return __('نام بازی');
            case 'App\Models\BookLoan':
                return __('نام کتاب');
            case 'App\Models\Offer':
                return __('نام کد تخفیف');
            case 'App\Models\Package':
                return __('نام بسته');
            case 'App\Models\Product':
                return __('نام محصول');
            default:
                return __('نام');
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function edited()
    {
        return $this->morphTo();
    }

    public function showIndex($model)
    {
        $reports = EditReport::where('edited_type', $this->models[$model] )->get();
        if($reports->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                <tr>
                    <th><?=__('ردیف')?></th>
                    <th>نام کاربر</th>
                    <th><?php echo $this->getEditedNameLabel($this->models[$model]) ?></th>
                    <th>کد</th>
                    <th>تاریخ</th>
                    <th><?=__('توضیحات')?></th>
                </tr>
                </thead>
                <tbody>
            <?php
            foreach($reports as $key => $report) {
                ?>
                <tr>
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $report->user?->name ?></td>
                    <td><?php echo $report->getEditedName() ?></td>
                    <td><?php echo $report->edited_id ?></td>
                    <td><?php echo timeFormat($report->created_at) ?></td>
                    <td><?php echo $report->details ?></td>
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
                    <div class="alert alert-danger text-center m-2"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
            <?php
        }
    }
}
