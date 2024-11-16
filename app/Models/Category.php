<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Category extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'name', 'parent_id', 'order', 'status', 'details', 'icon', 'image'];

    public function parent()
    {
        return $this->belongsTo(Category::class, "parent_id");
    }

    public function status()
    {
        switch ($this->status) {
            case 1:
                echo __("فعال");
                break;
            case 0:
                echo __("غیرفعال");
                break;
            default:
                echo __("نامشخص");
                break;
        }
    }

    public function showIndex()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        if($categories->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th><?=__('نام دسته')?></th>
                        <th><?=__('دسته والد')?></th>
                        <th><?=__('وضعیت')?></th>
                        <th><?=__('ترتیب نمایش')?></th>
                        <th><?=__('توضیحات')?></th>
                        <th><?=__('مدیریت')?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($categories as $category) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $category->name; ?></td>
                        <td><?php echo $category->parent?->name ?? __('ندارد'); ?></td>
                        <td><?php echo $category->status(); ?></td>
                        <td><?php echo $category->order;?></td>
                        <td><?php echo $category->details; ?></td>
                        <td>
                            <?php if (Gate::allows('update')) { ?>
                            <a href="#category-form" class="btn btn-warning btn-sm update-category" data-action="update" data-id="<?php echo $category->id ?>"
                                data-name="<?php echo $category->name ?>" data-parent_id="<?php echo $category->parent_id ?>" data-status="<?php echo $category->status ?>"
                                data-order="<?php echo $category->order ?>" data-details="<?php echo $category->details ?>">
                                <i class="bx bx-edit"></i>
                            </a>
                            <?php }
                            if (Gate::allows('delete')) { ?>
                            <button type="button" class="btn btn-danger btn-sm delete-category" data-id="<?php echo $category->id ?>"><i class="bx bx-trash"></i></button>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                </tbody>
            </table>
            <?php
        }
    }

    public function crud()
    {
        $categories = Category::latest()->get();
        $cat = new Category;
        $title = __("دسته بندی محصولات");
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <form id="category-form">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('نام')?> <span class="text-danger">*</span></label>
                        <input type="text" name="c-name" id="c-name" data-id="c-name" class="form-control checkEmpty" placeholder="<?=__('نام')?>...">
                        <div class="invalid-feedback" data-id="c-name" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('دسته والد')?></label>
                        <select name="c-parent-id" id="c-parent-id" class="form-select">
                            <option value=""><?=__('هیچکدام')?></option>
                            <?php
                            foreach ($categories as $category) {
                            ?>
                                <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('وضعیت')?></label>
                        <select name="c-status" id="c-status" class="form-select">
                            <option value="1"><?=__('فعال')?></option>
                            <option value="0"><?=__('غیرفعال')?></option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('ترتیب نمایش')?></label>
                        <input type="number" name="c-order" id="c-order" class="form-control just-numbers" placeholder="<?=__('ترتیب نمایش')?>...">
                    </div>
                </div>
                <!--div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label">آیکن</label>
                        <input type="text" name="c-icon" class="form-control" placeholder="آیکن...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">تصویر</label>
                        <input type="text" name="c-image" class="form-control" placeholder="تصویر...">
                    </div>
                </div-->
                <div class="row mb-3">
                    <div class="col-md-12 form-group">
                        <label class="form-label"><?=__('توضیحات')?></label>
                        <input type="text" name="c-details" id="c-details" class="form-control" placeholder="<?=__('توضیحات')?>...">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <input type="hidden" name="c-id" id="c-id" value="0">
                        <input type="hidden" name="c-action" id="c-action" value="create">
                        <button type="button" id="store-category" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
            <hr>
            <div id="category-result " class="table-responsive"><?php $cat->showIndex() ?></div>
        </div>
        <?php
    }

}
