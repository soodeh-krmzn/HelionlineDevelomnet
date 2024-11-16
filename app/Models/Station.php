<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class station extends Main
{
    use HasFactory;

    protected $fillable = ['name', 'show_status', 'details'];

    public function status()
    {
        if ($this->show_status == 1) {
            return '<span class="badge bg-success">'.__('فعال').'</span>';
        } else if($this->show_status == 0) {
            return '<span class="badge bg-danger">'.__('غیرفعال').'</span>';
        }
    }

    public function showIndex()
    {
        $stations = Station::orderBy('show_status', 'desc')->get();
        if($stations->count() > 0) {
            ?>
            <table id="station-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام') ?></th>
                        <th><?php echo __('توضیحات') ?></th>
                        <th><?php echo __('وضعیت') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($stations as $station) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $station->name; ?></td>
                        <td><?php echo $station->details; ?></td>
                        <td>
                            <?php
                            if ($station->show_status == 1) { ?>
                                <span class="badge bg-success"><?php echo __('فعال') ?></span>
                                <?php
                            } else if($station->show_status == 0) { ?>
                                <span class="badge bg-danger"><?php echo __('غیرفعال') ?></span>
                                <?php
                            }
                            ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $station->id; ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-section" data-id="<?php echo $station->id; ?>"><i class="bx bx-trash"></i></button>
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
            $title = __('ایستگاه جدید');
            $name = "";
            $show_status = "";
            $details = "";
        } else if ($action == "update") {
            $title = __('ویرایش ایستگاه');
            $station = Station::find($id);
            $name = $station->name;
            $show_status = $station->show_status;
            $details = $station->details;
        }
        ?>
        <form action="{{ route('saveStation') }}" method="post">
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="form-label required"><?php echo __('نام ایستگاه') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام ایستگاه') ?>..." value="<?php echo $name; ?>">
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
                        <button type="button" id="store-station" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                        <?php
                    } else {
                        ?>
                        <button type="button" id="store-station" data-action="update" data-id="<?php echo $id ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
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
        $stations = Station::where('show_status', 1)->orderBy('name', 'desc')->get();
        return $stations;
    }

}
