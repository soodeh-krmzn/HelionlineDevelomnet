<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'cage', 'cage_location', 'author', 'publisher', 'details'];

    public function showIndex()
    {
        $books = Book::orderBy('name', 'desc')->get();
        if($books->count() > 0) {
            ?>
            <table id="book-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام کتاب') ?></th>
                        <th><?php echo __('نام قفسه') ?></th>
                        <th><?php echo __('محل قفسه') ?></th>
                        <th><?php echo __('نام نویسنده') ?></th>
                        <th><?php echo __('نام ناشر') ?></th>
                        <th><?php echo __('توضیحات') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($books as $book) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $book->name; ?></td>
                        <td><?php echo $book->cage; ?></td>
                        <td><?php echo $book->cage_location; ?></td>
                        <td><?php echo $book->author; ?></td>
                        <td><?php echo $book->publisher; ?></td>
                        <td><?php echo $book->details; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $book->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-book" data-id="<?php echo $book->id; ?>"><i class="bx bx-trash"></i></button>
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
            $title = __('کتاب جدید');
            $name = "";
            $cage = "";
            $cage_location = "";
            $author = "";
            $publisher = "";
            $details = "";
        } else if ($action == "update") {
            $title = __('ویرایش کتاب');
            $book = Book::find($id);
            $name = $book->name;
            $cage = $book->cage;
            $cage_location = $book->cage_location;
            $author = $book->author;
            $publisher = $book->publisher;
            $details = $book->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?php echo __('نام کتاب') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام کتاب') ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نام قفسه') ?></label>
                    <input type="text" name="cage" id="cage" class="form-control" placeholder="<?php echo __('نام قفسه') ?>..." value="<?php echo $cage; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('محل قفسه') ?></label>
                    <input type="text" name="cage_location" id="cage-location" class="form-control" placeholder="<?php echo __('محل قفسه') ?>..." value="<?php echo $cage_location; ?>">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نویسنده') ?></label>
                    <input type="text" name="author" id="author" class="form-control" placeholder="<?php echo __('نویسنده') ?>..." value="<?php echo $author; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('ناشر') ?></label>
                    <input type="text" name="publisher" id="publisher" class="form-control" placeholder="<?php echo __('ناشر') ?>..." value="<?php echo $publisher; ?>">
                </div>
            </div>
            <div class="row mb-4">
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
                    <button type="button" id="store-book" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                    <?php
                } else if($action == "update") {
                    ?>
                    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                    <button type="button" id="store-book" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                    <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
            </div>
        </div>
        <?php
    }

}
