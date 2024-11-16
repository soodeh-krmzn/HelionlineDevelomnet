<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Main
{
    use HasFactory;

    protected $fillable = ['name', 'details'];

    public function showIndex()
    {
        $roles = Role::orderBy('name', 'desc')->get();
        if($roles->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th><?=__('نام نقش')?></th>
                        <th><?=__('توضیحات')?></th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($roles as $role) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $role->name; ?></td>
                        <td><?php echo $role->details; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $role->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-role" data-id="<?php echo $role->id; ?>"><i class="bx bx-trash"></i></button>
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
                    <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
            <?php
        }
    }

    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $title = __("نقش جدید");
            $name = "";
            $details = "";
        } else if ($action == "update") {
            $title = __("ویرایش نقش");
            $role = Role::find($id);
            $name = $role->name;
            $details = $role->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('نام نقش')?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('نام نقش')?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mt-2">
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('توضیحات')?></label>
                    <input type="text" name="details" id="details" class="form-control" placeholder="<?=__('توضیحات')?>..." value="<?php echo $details; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <?php
                if ($action == "create") { ?>
                    <button type="button" id="store-role" data-action="create" class="btn btn-success me-sm-3 me-1"><?=__('ثبت اطلاعات')?></button>
                    <?php
                } else {
                    ?>
                    <button type="button" id="store-role" data-id="<?php echo $id ?>" data-action="update" class="btn btn-warning me-sm-3 me-1"><?=__('ویرایش اطلاعات')?></button>
                    <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
            </div>
        </div>
        <?php
    }

}
