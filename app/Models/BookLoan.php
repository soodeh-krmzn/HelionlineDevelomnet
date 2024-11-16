<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyModels\Main;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookLoan extends Main
{
    use HasFactory;

    protected $fillable = ['user_id', 'person_id', 'book_id', 'return_date', 'status', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getStatusLabel()
    {
        if ($this->status == 1) {
            return '<span class="badge bg-success">'.__('تحویل داده شد').'</span>';
        }
        if ($this->return_date == null) {
            return '<span class="badge bg-warning">'.__('عدم تحویل').'</span>';
        }
        $return = new DateTime($this->return_date);
        $now = new DateTime();
        $diff = $now->diff($return)->format("%a");
        if ($return > $now) {
            return '<span class="badge bg-warning">' . $diff ." ". __('روز مانده تا تحویل').' </span>';
        }
        return '<span class="badge bg-danger">' . $diff ." ". __('روز گذشته از تحویل').' </span>';
    }

    public function showIndex($book_loans)
    {
        if($book_loans->count() > 0) {
            ?>
            <table id="loan-table" class="table table-hover border-top">
                <thead>
                <tr>
                    <th><?=__('ردیف')?></th>
                    <th>نام کاربر</th>
                    <th>نام مشتری</th>
                    <th>نام کتاب</th>
                    <th>تاریخ امانت</th>
                    <th>تاریخ بازگشت</th>
                    <th><?=__('وضعیت')?></th>
                    <th><?=__('توضیحات')?></th>
                    <th>مدیریت</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($book_loans as $loan) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $loan->user?->name; ?></td>
                        <td><?php echo $loan->person?->getFullName(); ?></td>
                        <td><?php echo $loan->book?->name; ?></td>
                        <td><?php echo dateFormat($loan->created_at); ?></td>
                        <td><?php echo dateFormat($loan->return_date) ; ?></td>
                        <td><?php echo $loan->status; ?></td>
                        <td><?php echo $loan->details; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $loan->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-loan" data-id="<?php echo $loan->id; ?>"><i class="bx bx-trash"></i></button>
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
            $title = __("امانت جدید");
            $user = "";
            $person_id = "";
            $book_id = "";
            $status = "";
            $return_date = "";
            $details = "";
        } else if ($action == "update") {
            $title = __("ویرایش کتاب");
            $loan = BookLoan::find($id);
            $user = $loan->user_id;
            $person_id = $loan->person_id;
            $book_id = $loan->book_id;
            $status = $loan->status;
            $return_date = $loan->return_date == null ? "" : dateFormat($loan->return_date);
            $details = $loan->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('کتاب')?> <span class="text-danger">*</span></label>
                    <select  name="book" id="book" class="form-select select2 checkEmpty">
                        <option value=""><?=__('انتخاب')?></option>
                        <?php foreach(Book::all() as $book) { ?>
                            <option value="<?php echo $book->id ?>" <?php echo $book_id == $book->id ? "selected" : "" ?>><?php echo $book->name ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('مشتری')?> <span class="text-danger">*</span></label>
                    <select required name="person" id="person" class="searchPersonL checkEmpty">
                    </select>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label"><?=__('تاریخ بازگشت')?></label>
                    <input type="text" name="return_date" id="return_date" class="form-control date-mask" value="<?php echo $return_date ?>" placeholder="1400/01/01">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('وضعیت')?> <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select checkEmpty">
                        <option value="0" <?php echo $status == 0 ? "selected" : "" ?>><?=__('عدم تحویل')?></option>
                        <option value="1" <?php echo $status == 1 ? "selected" : "" ?>><?=__('تحویل داده شد')?></option>
                    </select>
                </div>
            </div>
            <div class="row mb-4">
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
                    <button type="button" id="store-loan" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                    <?php
                } else if($action == "update") {
                    ?>
                    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                    <button type="button" id="store-loan" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                    <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
            </div>
        </div>
        <?php
    }

}
