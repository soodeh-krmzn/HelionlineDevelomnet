<?php

namespace App\Models;

use App\Models\User;
use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Course extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'start', 'price', 'sessions', 'capacity', 'details', 'user_id'];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function people()
    {
        return $this->belongsToMany(Person::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showIndex()
    {
        $courses = Course::latest()->get();
        if($courses->count() > 0) {
            ?><div class="table-responsive">
            <table id="course-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نام</th>
                        <th>مربی</th>
                        <th>قیمت</th>
                        <th>تاریخ شروع</th>
                        <th>تعداد جلسات</th>
                        <th>ظرفیت</th>
                        <th><?=__('توضیحات')?></th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($courses as $course) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $course->name; ?></td>
                        <td><?php echo $course->user?->getFullName(); ?></td>
                        <td><?php echo cnf($course->price); ?></td>
                        <td><?php echo DateFormat($course->start);?></td>
                        <td><?php echo $course->sessions; ?></td>
                        <td><?php echo cnf($course->capacity); ?></td>
                        <td><?php echo $course->details; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $course->id; ?>" data-bs-target="#crud" data-bs-toggle="modal"><i class="bx bx-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm delete-course" data-id="<?php echo $course->id; ?>"><i class="bx bx-trash"></i></button>
                            <button type="button" class="btn btn-success btn-sm register-course" data-id="<?php echo $course->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">ثبت نام</button>
                            <button type="button" class="btn btn-primary btn-sm people-course" data-id="<?php echo $course->id; ?>" data-bs-target="#people" data-bs-toggle="modal">افراد</button>
                            <a href="<?php echo route('session', ['course_id' => $course->id]) ?>" target="_blank" class="btn btn-info btn-sm" data-id="<?php echo $course->id; ?>">جلسات</a>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                </tbody>
            </table>
              </div>
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

    public function crud($action, $id)
    {
        $users = User::getUsers();
        if ($action == "create") {
            $title = __("کلاس جدید");
            $name = "";
            $user_id = "";
            $price = "";
            $start = "";
            $sessions = "";
            $capacity = "";
            $details = "";

        } else if ($action == "update") {
            $title = __("ویرایش کلاس");
            $course = Course::find($id);
            $name = $course->name;
            $user_id = $course->user_id;
            $price = $course->price;
            $start = DateFormat($course->start);
            $sessions = $course->sessions;
            $capacity = $course->capacity;
            $details = $course->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('نام')?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('نام')?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('مربی')?>  <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" data-id="user_id" class="form-select checkEmpty">
                        <option value=""><?=__('انتخاب کنید')?>...</option>
                        <?php
                        foreach ($users as $user) { ?>
                            <option value="<?php echo $user->id ?>" <?php echo $user_id == $user->id ?>><?php echo $user->getFullName() ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback" data-id="user_id" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('قیمت')?> <span class="text-danger">*</span></label>
                    <input type="text" name="price" data-id="price" class="form-control just-numbers money-filter checkEmpty" placeholder="<?=__('قیمت')?>..." value="<?php echo ($price != "" ? cnf($price) : ""); ?>">
                    <input type="hidden" name="price" id="price" data-id="price" class="form-control just-numbers checkEmpty" value="<?php echo $price ?>">
                    <div class="invalid-feedback" data-id="price" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                     <label class="form-label required"><?=__('تاریخ شروع')?></label>
                     <input type="text" name="start" id="start" data-id="start" class="form-control date-mask " placeholder="1401/01/01..." value="<?php echo $start ?>">
                     <div class="invalid-feedback" data-id="start" data-error="checkDate"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('ظرفیت')?></label>
                    <input type="text" name="capacity" data-id="capacity" class="form-control money-filter just-numbers" placeholder="<?=__('ظرفیت')?>..." value="<?php echo ($capacity != "" ? cnf($capacity) : ""); ?>">
                    <input type="hidden" name="capacity" id="capacity" data-id="capacity" class="form-control just-numbers" value="<?php echo $capacity ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('تعداد جلسات')?></label>
                    <input type="text" name="sessions" id="sessions" class="form-control" placeholder="<?=__('تعداد جلسات')?>..." value="<?php echo $sessions; ?>">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('توضیحات')?></label>
                    <input type="text" name="details" id="details" class="form-control" placeholder="<?=__('توضیحات')?>..." value="<?php echo $details ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-course" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <?php
                    } else if($action == "update") {
                        ?>
                        <button type="button" id="store-course" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                        <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                </div>
            </div>
        </div>
        <?php
    }

    public function showPeople()
    {
        $title = __("لیست افراد");
        $people = $this->people;
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <?php if ($people?-> count() > 0) { ?>
                <div class="table-responsive">
                <table id="course-table" class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th><?=__("لیست افراد")?></th>
                            <th><?=__("نام و نام خانوادگی")?></th>
                            <th><?=__('تاریخ ثبت نام')?></th>
                            <th><?=__('تعداد جلسات')?></th>
                            <th><?=__('مدیریت')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    foreach($people as $person) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $person->getFullName() . ' ( ' . $person->id . ' )'; ?></td>
                            <td><?php echo timeFormat($person->pivot->created_at); ?></td>
                            <td><?php echo cnf($person->countSessions($this->id)); ?></td>
                            <td>
                                <?php if (Gate::allows('delete')) { ?>
                                    <button type="button" class="btn btn-danger btn-sm sync-person" data-id="<?php echo $this->id; ?>" data-action="remove" data-person_id="<?php echo $person->id ?>"><i class="bx bx-trash"></i></button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
                </div>
                <?php
            } else { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                    </div>
                </div>
                <?php
            } ?>
        </div>
        <?php
    }

    public function showRegister()
    {

        $payment_type = new PaymentType;
        $title = __("ثبت نام");
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <form id="sync-person-form">
                <div class="row mb-4">
                    <div class="col-md-12 form-group">
                        <label class="form-label required"><?=__('انتخاب شخص')?> <span class="text-danger">*</span></label>
                        <select name="" id="person-id" data-id="person-id" class="form-select searchPersonL checkEmpty">
                        </select>
                        <div class="invalid-feedback" data-id="person-id" data-error="checkEmpty"></div>
                    </div>
                </div>
                <h1 class="text-center p-3 mb-3"><?=__('قابل پرداخت')?>: <?php echo cnf($this->price) ?></h1>

                <?php $payment_type->loadPaymentTypes($this->price); ?>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" id="sync-person" data-action="add" data-id="<?php echo $this->id ?>" class="btn btn-success me-sm-3 me-1 sync-person"><?=__('ثبت اطلاعات')?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}
