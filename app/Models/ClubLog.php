<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClubLog extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['person_id', 'rate', 'balance_rate', 'object_type', 'object_id', 'price', 'description'];

    public function object()
    {
        return $this->morphTo();
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function showIndex($logs)
    {
        if($logs->count() > 0) {
            ?>
            <table id="club-log-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نام</th>
                        <th>تاریخ</th>
                        <th>امتیاز</th>
                        <th>امتیاز کل</th>
                        <th>نوع</th>
                        <th><?=__('مبلغ')?></th>
                        <th><?=__('توضیحات')?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($logs as $log) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $log->person->getFullName(); ?></td>
                        <td><?php echo timeFormat($log->created_at); ?></td>
                        <td><?php echo $log->rate; ?></td>
                        <td><?php echo $log->balance_rate; ?></td>
                        <td><?php echo $log->object?->id; ?></td>
                        <td><?php echo cnf($log->price); ?></td>
                        <td><?php echo $log->description; ?></td>
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
}
