<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Counter extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $fillable = ['name', 'min'];

    public function showIndex()
    {
        $counters = Counter::orderBy('id', 'desc')->get();
        if ($counters->count() > 0) {
            ?>
            <table id="counter-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام شمارنده') ?></th>
                        <th><?php echo __('مقدار به دقیقه') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($counters as $counter) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $counter->name; ?></td>
                            <td><?php echo cnf($counter->min); ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $counter->id; ?>" data-bs-toggle="modal" data-bs-target="#crud"><i class="bx bx-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-counter" data-id="<?php echo $counter->id; ?>"><i class="bx bx-trash"></i></button>
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
        if ($action == "create") {
            $title = __('شمارنده جدید');
            $name = "";
            $minute = "";
        } else if ($action == "update") {
            $title = __('ویرایش شمارنده');
            $counter = Counter::find($id);
            $name = $counter->name;
            $minute = $counter->min;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label"><?php echo __('نام شمارنده') ?> <span class="text-danger">*</span></label>
                    <input type="text" id="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام شمارنده') ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label"><?php echo __('مقدار') ?> <small><?php echo __('(به دقیقه)') ?></small> <span class="text-danger">*</span></label>
                    <input type="text" data-id="minute" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('مقدار') ?>..." value="<?php echo ($minute != "" ? cnf($minute) : ""); ?>">
                    <input type="hidden" id="minute" data-id="minute" class="form-control just-numbers checkEmpty" value="<?php echo $minute; ?>">
                    <div class="invalid-feedback" data-id="minute" data-error="checkEmpty"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center demo-vertical-spacing">
                <?php
                if($action == "create") { ?>
                    <button type="button" id="store-counter" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                    <?php
                } else {
                    ?>
                    <button type="button" id="store-counter" data-action="update" data-id="<?php echo $counter->id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                    <?php
                }
                ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
            </div>
        </div>
        <?php
    }

    public function presentsInTimer($res)
    {
        $c = $res->count();

        $i = 1;
        if ($c > 0) {
            $ids = array();
            foreach ($res as $row) {
                array_push($ids, $row->id);

                $step = 100 / $row->counter_min;
                if($row->counter_passed == 0) {
                    $j = 0;
                } else {
                    $j = $row->counter_passed * $step;
                }

                ?>
                <div class="col-3 p-2 mb-4 counter" id="div<?php echo $row->id; ?>" data-id="<?php echo $row->id; ?>" data-bs-toggle="modal" data-bs-target="#crud">
                    <div class="progress-bar text-center resizable-counter-item">
                        <div id="ps<?php echo $row->id; ?>" data-time="<?php echo $row->counter_min; ?>" data-passed="<?php echo ($row->counter_passed == "") ? '0' : $row->counter_passed * 60; ?>" style="width: <?php echo $j; ?>; background: red;">
                            <p><b><?php echo $row->person_fullname; ?></b></p>
                            <p>
                                <span id="sec<?php echo $row->id; ?>"></span> <br>
                                <?php echo __('از') ?> <span id="counter-min<?php echo $row->id; ?>"><?php echo $row->counter_min; ?></span> <?php echo __('دقیقه') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id_<?php echo $row->id ?>" class="t-ids" value="<?php echo $row->id ?>">
                <?php
                $i++;
            }
            ?>
            <input type="hidden" class="ids" value="<?php echo implode(',', $ids); ?>">
            <?php
        }
    }

    public static function getSelect()
    {
        $counters = Counter::orderBy('name', 'desc')->get();
        return $counters;
    }

    public function edit($id)
    {
        $item=Game::find($id)->counter;
        $title = __('ویرایش شمارنده');
      return view('counter-v2.setting',compact('title','id','item'
      ))->render();
    }

}
