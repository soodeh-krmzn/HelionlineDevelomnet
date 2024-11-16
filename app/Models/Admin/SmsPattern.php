<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsPattern extends Admin
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'category_id', 'name', 'text', 'page', 'cost'];

    public function category()
    {
        return $this->belongsTo(SmsPatternCategory::class, 'category_id');
    }

    public function showIndex()
    {
        $smsPatterns = SmsPattern::orderBy('id', 'desc')->get();
        if($smsPatterns->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>دسته</th>
                        <th>متن پیامک</th>
                        <th>صفحه</th>
                        <th>هزینه</th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($smsPatterns as $smsPattern) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $smsPattern->category?->name; ?></td>
                        <td><?php echo $smsPattern->text; ?></td>
                        <td><?php echo $smsPattern->page; ?></td>
                        <td><?php echo cnf($smsPattern->cost); ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $smsPattern->id; ?>" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-sms-pattern" data-id="<?php echo $smsPattern->id; ?>"><i class="bx bx-trash"></i></button>
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

    public function crud($action, $id = 0, $smsPatternCategory)
    {
        if ($action == "create") {
            $title = "افزودن پیامک جدید";
            $category_id = "";
            $text = "";
            $page = "";
            $cost = "";
        } else if ($action == "update") {
            $title = "ویرایش پیامک";
            $smsPattern = SmsPattern::find($id);
            $category_id = $smsPattern->category_id;
            $text = $smsPattern->text;
            $page = $smsPattern->page;
            $cost = $smsPattern->cost;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 form-group">
                    <label class="form-label required">دسته <span class="text-danger">*</span></label>
                    <?php
                    if($smsPatternCategory->getSelect()->count() > 0) {
                        ?>
                        <select id="category-id" data-id="category-id" class="form-select checkEmpty">
                            <?php
                            foreach($smsPatternCategory->getSelect() as $smsPatternCategoryRow) {
                                ?>
                                <option value="<?php echo $smsPatternCategoryRow->id; ?>"><?php echo $smsPatternCategoryRow->name; ?></option>
                                <?php
                            } ?>
                        </select>
                        <?php
                    } else {
                        ?>
                        <span class="badge d-block bg-danger">بدون دسته</span>
                        <?php
                    }
                    ?>
                    <div class="invalid-feedback" data-id="category-id" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label required">صفحه <span class="text-danger">*</span></label>
                    <select id="page" data-id="page" class="form-select">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                    <div class="invalid-feedback" data-id="page" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-4 form-group">
                    <label class="form-label required">هزینه <span class="text-danger">*</span></label>
                    <input id="cost" data-id="cost" class="form-control" placeholder="هزینه..." value="<?php echo $cost; ?>">
                    <div class="invalid-feedback" data-id="cost" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label required">متن پیامک <span class="text-danger">*</span></label>
                    <textarea id="text" data-id="text" class="form-control" placeholder="متن پیامک..."><?php echo $text; ?></textarea>
                    <div class="invalid-feedback" data-id="text" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-sms-pattern" data-action="create" class="btn btn-success me-sm-3 me-1"><?=__('ثبت اطلاعات')?></button>
                        <?php
                    } else {
                        ?>
                        <button type="button" id="store-sms-pattern" data-action="update" data-id="<?php echo $id ?>" class="btn btn-warning me-sm-3 me-1"><?=__('ویرایش اطلاعات')?></button>
                        <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                </div>
            </div>
        </div>
        <?php
    }

    public static function getSelectByCategory($categoryId)
    {
        $smsPatterns = SmsPattern::where('category_id', $categoryId)->get();
        return $smsPatterns;
    }

    public function showIndex1()
    {
        $SmsPatternCategory = new SmsPatternCategory;
        $SmsPattern = new SmsPattern;
        $setting = new Setting;
        if($SmsPatternCategory->getSelect()->count() > 0) {
            foreach($SmsPatternCategory->getSelect() as $SmsPatternCategoryRow) {
            ?>
            <div class="col-12">
                <h3><?php echo $SmsPatternCategoryRow->name ?></h3>
                <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="table-warning dark-tr">
                            <th>ردیف</th>
                            <th style="min-width: 180px;">عنوان پیام</th>
                            <th>صفحه</th>
                            <th>هزینه</th>
                            <th><?=__('وضعیت')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($SmsPattern->getSelectByCategory($SmsPatternCategoryRow->id)->count() > 0) {
                            $i = 1;
                            foreach($SmsPattern->getSelectByCategory($SmsPatternCategoryRow->id) as $SmsPatternRow) {
                                ?>
                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td>
                                        <textarea class="form-control" rows="4" readonly><?php echo $SmsPatternRow->text ?></textarea>
                                    </td>
                                    <td><?php echo $SmsPatternRow->page ?></td>
                                    <td><?php echo cnf($SmsPatternRow->cost) ?></td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input status-sms" data-id="<?php echo $SmsPatternRow->id ?>" <?php echo $setting->getSetting($SmsPatternCategoryRow->name) == $SmsPatternRow->id ? "checked" : "" ?>>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on">
                                                    <i class="bx bx-check"></i>
                                                </span>
                                                <span class="switch-off">
                                                    <i class="bx bx-x"></i>
                                                </span>
                                            </span>
                                        </label>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="100%" class="text-center">هیچ الگوی پیامی در این دسته تعریف نشده است.</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                </div>
            </div>
            <?php
            }
        } else {
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger text-center m-0">
                    هیچ دسته پیامی تعریف نشده است.
                </div>
            </div>
            <?php
        }
    }

}
