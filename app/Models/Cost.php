<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cost extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['created_by', 'price', 'details','date'];

    public function categories()
    {
        return $this->belongsToMany(CostCategory::class, "cost_category", "cost_id", "category_id");
    }

    public function categoriesInString() {
        return $this->categories->pluck('name')->join(',');
    }
    public function user()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function showIndex()
    {
        return false;
        $costs = Cost::all();
        if ($costs->count() > 0) { ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>دسته</th>
                        <th><?=__('مبلغ')?></th>
                        <th><?=__('توضیحات')?></th>
                        <th>تاریخ</th>
                        <th>کاربر</th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($costs as $cost) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>
                            <?php
                            foreach ($cost->categories as $category) {
                                echo $category->name . '، ';
                            } ?>
                        </td>
                        <td><?php echo cnf($cost->price); ?></td>
                        <td><?php echo $cost->details; ?></td>
                        <td><?php echo timeFormat($cost->created_at); ?></td>
                        <td><?php echo $cost->created_by; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $cost->id; ?>" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="bx bx-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-cost" data-id="<?php echo $cost->id ?>"><i class="bx bx-trash"></i></button>
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
            <div class="row">
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
            $cost = new Cost;
            $price = $cost->price;
            $details = $cost->details;
        } else if ($action == "update") {
            $cost = Cost::find($id);
            $price = $cost->price;
            $details = $cost->details;
        }
        if (CostCategory::all()->count() > 0) {
            ?>
            <form class="add-new-user pt-0" id="cost-form" onsubmit="return false">
                <div class="mb-3">
                    <label class="form-label"><?=__('مبلغ')?> <span class="text-danger">*</span></label>
                    <input type="text" data-id="price" class="form-control  money-filter checkEmpty" placeholder="<?=__('مبلغ')?>..." value="<?php echo ($price != "" ? ($price) : ""); ?>">
                    <input type="hidden" id="price" data-id="price" class="form-control just-numbers checkEmpty" value="<?php echo $price; ?>">
                    <div class="invalid-feedback" data-id="price" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?=__('توضیحات')?> <span class="text-danger">*</span></label>
                    <input type="text" id="details" data-id="details" class="form-control checkEmpty" placeholder="<?=__('توضیحات')?>..." value="<?php echo $details; ?>">
                    <div class="invalid-feedback" data-id="details" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?=__('دسته')?> <span class="text-danger">*</span></label>
                    <select name="categories" id="categories" data-id="categories" class="form-select c-select2 checkEmpty" multiple>
                        <?php
                        foreach (CostCategory::all() as $costCategory) {
                            ?>
                            <option value="<?php echo $costCategory->id ?>" <?php echo $cost->categories->contains($costCategory->id) ? "selected" : "" ?>><?php echo $costCategory->name ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback" data-id="categories" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?=__('تاریخ')?></label>
                    <input type="text" placeholder="زمان حال" value="<?=dateFormat($cost?->date)?>" class=" form-control date-mask" name="date"  id="date">
                </div>
                <div class="col-12 text-center">
                    <?php
                    if($action == "create") { ?>
                        <input type="hidden" id="id" value="0">
                        <button type="button" id="store-cost" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <?php
                    } else if ($action == "update") {
                        ?>
                        <input type="hidden" id="id" value="<?php echo $id; ?>">
                        <button type="button" id="store-cost" data-action="update" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                        <?php
                    }
                    ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas"><?=__('انصراف')?></button>
                </div>
            </form>
            <?php
        } else {
            ?>
            <div class="alert alert-danger text-center"><?=__('لطفا ابتدا دسته بندی هزینه ها را تعریف نمایید.')?></div>
            <?php
        }
    }

}
