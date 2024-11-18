<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Product extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $fillable = ['id', 'name', 'stock', 'buy', 'sale', 'cart', 'image', 'status'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, "product_category");
    }

    public function status()
    {
        if ($this->status == 0) {
            return '<span class="badge bg-danger">'.__('غیرفعال').'</span>';
        } else {
            return '<span class="badge bg-success">'.__('فعال').'</span>';
        }
    }

    public function getStock()
    {
        if ($this->stock > 0) {
            return '<span class="badge bg-success">' . cnf($this->stock) . '</span>';
        } else {
            return '<span class="badge bg-danger">' . cnf($this->stock) . '</span>';
        }
    }

    public function crudStock($id)
    {
        $title = __('مدیریت موجودی');
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <form id="stock-form">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12 form-group">
                        <label class="form-label"><?php echo __('مقدار') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="change" data-id="change" class="form-control checkEmpty just-numbers money-filter" placeholder="<?php echo __('مقدار') ?>...">
                        <input type="hidden" name="change" id="change" data-id="change" class="form-control just-numbers checkEmpty">
                        <div class="invalid-feedback" data-id="change" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <button type="button" data-id="<?php echo $id ?>" data-sign="-1" class="btn btn-danger update-stock me-sm-3 me-1"><?php echo __('کاهش') ?></button>
                        <button type="button" data-id="<?php echo $id ?>" data-sign="1" class="btn btn-success update-stock me-sm-3 me-1"><?php echo __('افزایش') ?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function editStock($change)
    {
        $old_stock = $this->stock;
        $new_stock = $this->stock + $change;
        $this->stock = $new_stock;
        $this->save();

        if ($change != 0) {
            EditReport::create([
                'user_id' => auth()->user()->id,
                'edited_type' => 'App\Models\Product',
                'edited_id' => $this->id,
                'details' => 'تغییر موجودی از ' . cnf($old_stock) . ' به ' . cnf($new_stock)
            ]);
        }
    }

    public function showIndex($products)
    {
        if ($products->count() > 0) {
            ?>
            <table id="product-table" class="table table-hover border-top">
                <thead>
                <tr>
                    <th><?php echo __('ردیف') ?></th>
                    <th><?php echo __('نام کالا') ?></th>
                    <th><?php echo __('موجودی') ?></th>
                    <th><?php echo __('قیمت خرید') ?></th>
                    <th><?php echo __('قیمت فروش') ?></th>
                    <th><?php echo __('تصویر محصول') ?></th>
                    <th><?php echo __('وضعیت') ?></th>
                    <th><?php echo __('مدیریت') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($products as $product) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $product->name; ?></td>
                        <td><?php echo $product->getStock(); ?></td>
                        <td><?php echo $product->buy; ?></td>
                        <td><?php echo $product->sale; ?></td>
                        <td>
                            <?php
                            if ($product->image != null) { ?>
                                <img src="<?php echo $product->image; ?>" class="product-image">
                                <?php
                            } ?>
                        </td>
                        <td><?php echo $product->status(); ?></td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm crud-stock" data-id="<?php echo $product->id; ?>" data-bs-toggle="modal" data-bs-target="#stock-modal"><i class="bx bxs-cube"></i></button>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $product->id; ?>" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="bx bx-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-product" data-id="<?php echo $product->id ?>"><i class="bx bxs-trash"></i></button>
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
            $product = new Product;
            $name = "";
            $stock = "";
            $buy = "";
            $sale = "";
            $image = "";
            $status = "";
        } else if ($action == "update") {
            $product = Product::find($id);
            $name = $product->name;
            $stock = $product->stock;
            $buy = $product->buy;
            $sale = $product->sale;
            $image = $product->image;
            $status = $product->status;
        }
        if (Category::all()->count() > 0) {
            ?>
            <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label"><?php echo __('نام') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام') ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('موجودی') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="stock" data-id="stock" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('موجودی') ?>..." value="<?php echo ($stock != "" ? cnf($stock) : ""); ?>">
                    <input type="hidden" name="stock" id="stock" data-id="stock" class="form-control just-numbers checkEmpty" value="<?php echo $stock; ?>">
                    <div class="invalid-feedback" data-id="stock" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('قیمت خرید') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="buy" data-id="buy" class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('قیمت خرید') ?>..." value="<?php echo ($buy != "" ? cnf($buy) : ""); ?>">
                    <input type="hidden" name="buy" id="buy" data-id="buy" class="form-control just-numbers checkEmpty" value="<?php echo $buy; ?>">
                    <div class="invalid-feedback" data-id="buy" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('قیمت فروش') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="sale" data-id="sale" class="form-control text-start just-numbers money-filter checkEmpty" placeholder="<?php echo __('قیمت فروش') ?>..." value="<?php echo ($sale != "" ? cnf($sale) : ""); ?>">
                    <input type="hidden" name="sale" id="sale" data-id="sale" class="form-control text-start just-numbers checkEmpty" value="<?php echo $sale; ?>">
                    <div class="invalid-feedback" data-id="sale" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('دسته ها') ?> <span class="text-danger">*</span></label>
                    <select name="categories" id="categories" data-id="categories" class="form-select select2 checkEmpty" multiple>
                        <?php
                        foreach (Category::all() as $category) { ?>
                            <option value="<?php echo $category->id ?>" <?php echo $product?->categories->contains($category->id) ? "selected" : "" ?>><?php echo $category->name ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback" data-id="categories" data-error="checkEmpty"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('وضعیت نمایش') ?></label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" <?php echo $status == 1 ? "selected" : "" ?>><?php echo __('فعال') ?></option>
                        <option value="0" <?php echo $status == 0 ? "selected" : "" ?>><?php echo __('غیرفعال') ?></option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('تصویر محصول') ?></label>
                    <input type="file" name="image" id="image" class="form-control text-start" placeholder="<?php echo __('تصویر محصول') ?>..." value="<?php echo $image; ?>">
                    <input type="hidden" name="no_image" id="no-image">
                    <div class="form-image-container mt-2 <?php echo $image == '' ? 'd-none' : '' ?>" id="old-image-container">
                        <img src="<?php echo $image ?>" class="form-image">
                        <button class="btn btn-danger btn-xs delete-image" id="delete-old-image"><i class="bx bx-trash"></i></button>
                    </div>
                    <div class="form-image-container mt-2" style="display: none;" id="new-image-container">
                        <img src="" class="form-image" id="new-image">
                        <button class="btn btn-danger btn-xs delete-image" id="delete-new-image"><i class="bx bx-trash"></i></button>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <input type="hidden" name="id" id="id" value="0">
                        <input type="hidden" name="action" id="action" value="create">
                        <button type="button" id="store-product" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                        <?php
                    } else if ($action == "update") { ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="action" id="action" value="update">
                        <button type="button" id="store-product" data-action="update" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                        <?php
                    }
                    ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas"><?php echo __('انصراف') ?></button>
                </div>
            </form>
            <?php
        } else {
            ?>
            <div class="alert alert-danger text-center"><?php echo __('لطفا ابتدا دسته بندی محصولات را تعریف نمایید.') ?></div>
            <?php
        }
    }

}
