<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['course_id', 'date', 'details'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class);
    }

    public function showIndex(Course $course)
    {
        $sessions = $course->sessions()->latest()->get();
        if ($sessions->count() > 0) {
?>
            <table id="session-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>تاریخ</th>
                        <th><?=__('توضیحات')?></th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($sessions as $session) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo DateFormat($session->date); ?></td>
                            <td><?php echo $session->details; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $session->id; ?>" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>
                                <button type="button" class="btn btn-danger btn-sm delete-session" data-id="<?php echo $session->id; ?>"><i class="bx bx-trash"></i></button>
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

    public function crud($action, $id, $course_id)
    {
        $course = Course::find($course_id);
        $people = $course->people;
        if ($action == "create") {
            $session = new Session;
            $date = "";
            $details = "";
            $title = __("حضور و غیاب جدید");
        } else if ($action == "update") {
            $session = Session::find($id);
            $date = DateFormat($session->date);
            $details = $session->details;
            $title = __("ویرایش حضور و غیاب");
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <form id="category-form">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('تاریخ')?> <span class="text-danger">*</span></label>
                        <input type="text" name="date" id="date" data-id="date" class="form-control date-mask checkEmpty " value="<?php echo $date ?>" placeholder="1401/01/01">
                        <div class="invalid-feedback" data-id="date" data-error="checkEmpty"></div>
                        <div class="invalid-feedback" data-id="date" data-error="checkDate"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('توضیحات')?> <span class="text-danger">*</span></label>
                        <input type="text" name="details" id="details" data-id="details" class="form-control checkEmpty" value="<?php echo $details ?>" placeholder="<?=__('توضیحات')?>...">
                        <div class="invalid-feedback" data-id="details" data-error="checkEmpty"></div>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <?php if ($people?->count() > 0) { ?>
                        <div class="table-responsive">
                            <table id="people-table" class="table table-hover border-top">
                                <thead>
                                    <tr>
                                        <th><?=__('ردیف')?></th>
                                        <th><?=__('نام و نام خانوادگی')?></th>
                                        <th><?=__('مدیریت')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <form>
                                        <?php
                                        $i = 1;
                                        foreach ($people as $person) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td>
                                                    <?php echo $person->getFullName();
                                                    if ($person->balance < 0) { ?>
                                                        <br>
                                                        <span class="badge bg-danger"><?php echo abs($person->balance) ?> <?=__('بدهکار')?></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <select name="<?php echo $person->id ?>" class="form-select person-id">
                                                        <option value="1" <?php echo $action == "update" ? ($session->people->contains($person->id) ? "selected" : "") : "selected" ?>><?=__('حاضر')?></option>
                                                        <option value="0" <?php echo $action == "update" ? ($session->people->contains($person->id) ? "" : "selected") : "" ?>><?=__('غایب')?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </form>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <?php
                        if ($action == "create") { ?>
                            <button type="button" id="store-session" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <?php
                        } else {
                        ?>
                            <button type="button" id="store-session" data-action="update" data-id="<?php echo $id ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                        <?php
                        } ?>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
        </div>
<?php
    }
}
