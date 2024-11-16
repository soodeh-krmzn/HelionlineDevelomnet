<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsPatternCategory extends Admin
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'name', 'display_order'];

    public function showIndex()
    {
        $smsPatternCategories = SmsPatternCategory::orderBy('name', 'desc')->get();
        if($smsPatternCategories->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>نام</th>
                    <th>مدیریت</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($smsPatternCategories as $smsPatternCategory) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $smsPatternCategory->name; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $smsPatternCategory->id; ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-sms-pattern-category" data-id="<?php echo $smsPatternCategory->id; ?>"><i class="bx bx-trash"></i></button>
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
            $title = "افزودن دسته پیامک جدید";
            $name = "";
        } else if ($action == "update") {
            $title = "ویرایش دسته پیامک";
            $smsPatternCategory = SmsPatternCategory::find($id);
            $name = $smsPatternCategory->name;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
               <div class="col-md-12 form-group">
                    <label class="form-label required">نام دسته <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="نام دسته..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
               </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-sms-pattern-category" data-action="create" class="btn btn-success me-sm-3 me-1"><?=__('ثبت اطلاعات')?></button>
                        <?php
                    } else {
                        ?>
                        <button type="button" id="store-sms-pattern-category" data-action="update" data-id="<?php echo $id ?>" class="btn btn-warning me-sm-3 me-1"><?=__('ویرایش اطلاعات')?></button>
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
        $smsPatternCategories = SmsPatternCategory::orderBy('display_order', 'asc')->get();
        return $smsPatternCategories;
    }

}
