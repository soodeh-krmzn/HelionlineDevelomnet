<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adjective extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function showIndex()
    {
        $adjectives = Adjective::orderBy('id', 'desc')->get();
        if($adjectives->count() > 0) {
            ?>
            <table id="adjective-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام امانتی') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach($adjectives as $adjective) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $adjective->name; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $adjective->id; ?>" data-bs-toggle="modal" data-bs-target="#crud"><i class="bx bx-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-adjective" data-id="<?php echo $adjective->id; ?>"><i class="bx bx-trash"></i></button>
                            </td>
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
                    <div class="alert alert-danger text-center m-0"><?php echo __('موردی جهت نمایش موجود نیست.') ?></div>
                </div>
            </div>
            <?php
        }
    }

    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $title = __('امانتی جدید');
            $name = "";
        } else if ($action == "update") {
            $title = __('ویرایش امانتی');
            $adjective = Adjective::find($id);
            $name = $adjective->name;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="form-label"><?php echo __('نام امانتی') ?> <span class="text-danger">*</span></label>
                    <input type="text" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام امانتی') ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center demo-vertical-spacing">
                <?php
                if($action == "create") { ?>
                    <button type="button" id="store-adjective" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                    <?php
                } else {
                    ?>
                    <button type="button" id="store-adjective" data-action="update" data-id="<?php echo $id ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                    <?php
                }
                ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
            </div>
        </div>
        <?php
    }

    public static function getSelect()
    {
        $adjectives = Adjective::orderBy('name', 'desc')->get();
        return $adjectives;
    }

}
