<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use App\Models\Game;
use App\Models\Setting;
use App\Models\MyModels\Main;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Section extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $fillable = ['id', 'name', 'show_status', 'details','type'];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function status()
    {
        $setting = new Setting;
        $default_section = $setting->getSetting('default_section');
        if ($default_section == $this->id) {
            $def = '<span class="badge bg-info">پیشفرض</small>';
        } else {
            $def = '';
        }
        if ($this->show_status == 1) {
            return '<span class="badge bg-success">'.__('فعال').'</span>&nbsp' . $def;
        } else if($this->show_status == 0) {
            return '<span class="badge bg-danger">'.__('غیرفعال').'</span>&nbsp' . $def;
        }
    }

    public function showIndex()
    {
        $sections = Section::orderBy('show_status', 'desc')->get();
        if($sections->count() > 0) {
            ?>
            <table id="section-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام بخش') ?></th>
                        <th><?php echo __('توضیحات') ?></th>
                        <th><?php echo __('وضعیت') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($sections as $section) {
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>q<?php echo $section->name; ?></td>
                        <td><?php echo $section->details; ?></td>
                        <td>
                            <?php
                            if ($section->show_status == 1) { ?>
                                <span class="badge bg-success"><?=__('فعال')?></span>
                                <?php
                            } else if($section->show_status == 0) { ?>
                                <span class="badge bg-danger"><?=__('غیرفعال')?></span>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $section->id; ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-section" data-id="<?php echo $section->id; ?>"><i class="bx bx-trash"></i></button>
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
        $setting = new Setting;
        $default_section = $setting->getSetting('default_section');

        if ($action == "create") {
            $title = __('بخش جدید');
            $name = "";
            $show_status = "";
            $details = "";
        } else if ($action == "update") {
            $title = __('ویرایش بخش');
            $section = Section::find($id);
            $name = $section->name;
            $show_status = $section->show_status;
            $details = $section->details;
        }
        ?>
        <form action="{{ route('saveSection') }}" method="post">
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="form-label required"><?php echo __('نام بخش') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام بخش') ?>..." value="<?php echo $name; ?>">
                        <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 form-group">
                        <label class="form-label required"><?php echo __('وضعیت') ?> <span class="text-danger">*</span></label>
                        <select name="show_status" id="show-stauts" data-id="show_status" class="form-control checkEmpty">
                            <option <?php echo ($show_status == 1) ? "selected" : ""; ?> value="1"><?php echo __('فعال') ?></option>
                            <option <?php echo ($show_status == 0) ? "selected" : ""; ?> value="0"><?php echo __('غیرفعال') ?></option>
                        </select>
                        <div class="invalid-feedback" data-id="show_status" data-error="checkEmpty"></div>
                    </div>
                </div>
                <?php
                if ($id > 0) { ?>
                    <div class="row mt-2">
                        <label class="form-label"><?php echo __('پیشفرض') ?></label>
                        <label class="switch switch-success switch-lg">
                            <input type="checkbox" class="switch-input update-default-section" data-id="<?php echo $id ?>" <?php echo ($default_section == $id) ? "checked" : "" ?>>
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="bx bx-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="bx bx-x"></i>
                                </span>
                            </span>
                        </label>
                    </div>
                    <?php
                } ?>
                <div class="row mt-2">
                    <div class="col-md-12 form-group">
                        <label class="form-label required"><?php echo __('توضیحات') ?></label>
                        <input type="text" name="details" id="details" class="form-control" placeholder="<?php echo __('توضیحات') ?>..." value="<?php echo $details; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-section" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                        <?php
                    } else {
                        ?>
                        <button type="button" id="store-section" data-action="update" data-id="<?php echo $id ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                        <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
                </div>
            </div>
        </form>
        <?php
    }

    public static function getSelect()
    {
        $sections = Section::where('show_status', 1)->orderBy('name', 'desc')->get();
        return $sections;
    }

    public function getMonthVisitList($from_date, $to_date)
    {
        $arr = array();
        $days = (new DateTime($from_date))->diff(new DateTime($to_date))->format('%a');
        $new_from_date = $from_date->copy();
        for ($day = 0; $day < $days; $day++) {
            $total = Game::where('section_id', $this->id)
                ->whereDate('created_at', $new_from_date)
                ->get()->count();
            array_push($arr, $total);
            $new_from_date->addDay();
        }
        return $arr;
    }

    public function getHourlyVisitList($date)
    {
        $arr = array();
        for ($i = 0; $i < 24; $i++) {
            $count = Game::where('section_id', $this->id)
                ->where('in_date', $date)
                ->where('in_time', '>=', $i .':00:00')
                ->where('in_time', '<=', $i .':59:59')
                ->get()->count();
            $arr[] = $count;
        }
        return $arr;
    }

    public function getHoursOfVisitList($date)
    {
        $arr = array();
        $games = Game::where('section_id', $this->id)
            ->where('in_date', $date)
            ->get();
        foreach ($games as $game) {
            $time1 = new DateTime($game->in_date . ' ' . $game->in_time);
            $time2 = new DateTime($game->out_date . ' ' . $game->out_time);
            $interval = $time1->diff($time2);
            $minutes = ($interval->d * 24 * 60) + ($interval->h * 60) + $interval->i;

            $arr[] = [(int)$time1->format('H'), $minutes];
        }
        return $arr;
    }

}
