<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use App\Traits\Syncable;

class Person extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $fillable = ['id', 'created_by', 'name', 'family', 'fname', 'birth', 'shamsi_birth', 'address', 'card_code', 'gender', 'mobile', 'sharj', 'expire', 'pack', 'commitment', 'profile', 'club', 'rate', 'reg_code', 'national_code', 'wallet_value', 'balance'];

    public function getGenderLabel($gender)
    {
        if ($gender == 0) {
            return "<span class='badge bg-warning'>دختر</span>";
        } else if ($gender == 1) {
            return "<span class='badge bg-success'>پسر</span>";
        }
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->family;
    }

    public function isNotExpired()
    {
        return (today()->lte($this->expire) && $this->sharj > 0); // TODO: check expire
    }

    public function LastBirthSmsInfo()
    {
        $logs = SmsLog::where('recipient', $this->mobile)->where('category_name', 'تولد')->latest()->get();
        if (count($logs) > 0) {
            return [
                'lastDate' => timeFormat($logs->first()->created_at),
                'total' => count($logs)
            ];
        }
        return false;
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'pack');
    }

    public function getMeta($key)
    {
        $meta = PersonMeta::where('p_id', $this->id)->where('meta', $key)->first();
        return $meta->value ?? "";
    }

    public function getSubmitShamsiAttribute()
    {
        return persianTime($this->created_at);
    }
    public function getBirthShamsiAttribute()
    {
        return persianTime($this->birth);
    }
    public function groupNames(){
        return $this->belongsToMany(Group::class,'group_person', 'group_id','person_id')->pluck('name')->implode(' - ');
    }
    public function getBalance()
    {
        if ($this->balance > 0) {
            echo cnf($this->balance) . " طلبکار";
        } else if ($this->balance < 0) {
            echo cnf($this->balance) . " بدهکار";
        } else if ($this->balance == 0) {
            echo 0;
        }
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function activeGame()
    {
        return $this->games->where("status", 0)->first();
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class)->withPivot('course_id');
    }

    public function countSessions($course_id)
    {
        return $this->sessions()->wherePivot('course_id', $course_id)->get()?->count();
    }

    public function showIndex($people)
    {
        if ($people->count() > 0) {
?>
            <table id="people-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>شماره عضویت</th>
                        <th>ثبت نام</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>تاریخ تولد</th>
                        <th>کد اشتراک</th>
                        <th>جنسیت</th>
                        <th>موبایل</th>
                        <th>تعهدنامه</th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($people as $person) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $person->id; ?></td>
                            <td><?php echo timeFormat($person->created_at); ?></td>
                            <td><?php echo $person->name; ?></td>
                            <td><?php echo $person->family; ?></td>
                            <td><?php echo dateFormat($person->birth); ?></td>
                            <td><?php echo $person->reg_code; ?></td>
                            <td><?php echo $this->getGenderLabel($person->gender); ?></td>
                            <td><?php echo $person->mobile; ?></td>
                            <td></td>
                            <td>
                                <?php if (Gate::allows('update')) { ?> <button type="button" class="btn btn-info btn-sm meta" data-id="<?php echo $person->id; ?>" data-bs-toggle="modal" data-bs-target="#meta"><i class="bx bx-user"></i></button><?php } ?>
                                <?php if (Gate::allows('update')) { ?> <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $person->id; ?>" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="bx bx-edit"></i></button> <?php } ?>
                                <?php if (Gate::allows('delete')) { ?> <button class="btn btn-danger btn-sm" id="delete-person" data-id="<?php echo $person->id ?>"><i class="bx bx-trash"></i></button> <?php } ?>
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
                    <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
        <?php
        }
    }



    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $name = "";
            $family = "";
            $mobile = "";
            $birth = "";
            $gender = "";
            $national_code = "";
            $reg_code = "";
            $club = "";
        } else if ($action == "update") {
            $person = Person::find($id);
            $name = $person->name;
            $family = $person->family;
            $mobile = $person->mobile;
            $birth = $person->birth ? dateFormat($person->birth) : dateFormat($person->shamsi_birth);
            $gender = $person->gender;
            $national_code = $person->national_code;
            $reg_code = $person->reg_code;
            $club = $person->club;
        }
        ?>
        <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"><?=__('نام')?> <span class="text-danger">*</span></label>
                    <input type="text" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('نام')?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><?=__('نام خانوادگی')?><span class="text-danger">*</span></label>
                    <input type="text" id="family" data-id="family" class="form-control checkEmpty" placeholder="<?=__('نام خانوادگی')?>..." value="<?php echo $family; ?>">
                    <div class="invalid-feedback" data-id="family" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?=__('شماره موبایل')?> <span class="text-danger">*</span></label>
                <input type="text" id="mobile" data-id="mobile" class="form-control just-numbers checkMobile checkEmpty" placeholder="<?=__('شماره موبایل')?>..." value="<?php echo $mobile; ?>" maxlength="11">
                <div class="invalid-feedback" data-id="mobile" data-error="checkEmpty"></div>
                <div class="invalid-feedback" data-id="mobile" data-error="checkMobile"></div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?=__('جنسیت')?> <span class="text-danger">*</span></label>
                <select name="gender" id="gender" data-id="gender" class="form-select checkEmpty">
                    <option value=""><?=__('انتخاب')?></option>
                    <option <?php echo ($gender == 0) ? "selected" : ""; ?> value="0"><?=__('دختر')?></option>
                    <option <?php echo ($gender == 1) ? "selected" : ""; ?> value="1"><?=__('پسر')?></option>
                </select>
                <div class="invalid-feedback" data-id="gender" data-error="checkEmpty"></div>
            </div>
            <div class="mb-3">
                <label class="form-label"> <?=__('کدملی')?> </label>
                <input type="text" id="national_code" data-id="national_code" class="form-control text-start just-numbers checkNationalCode" placeholder=" <?=__('کدملی')?>..." value="<?php echo $national_code; ?>" maxlength="10">
                <div class="invalid-feedback" data-id="national_code" data-error="checkNationalCode"></div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?=__('کد اشتراک')?></label>
                <input type="text" id="reg_code" class="form-control text-start" placeholder="<?=__('کد اشتراک')?>..." value="<?php echo $reg_code; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?=__('تاریخ تولد')?></label>
                <input type="text" id="birth" data-id="birth" class="form-control text-start date-mask " placeholder="1401/01/01" value="<?php echo $birth; ?>">
                <div class="invalid-feedback" data-id="birth" data-error="checkDate"></div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?=__('باشگاه مشتریان')?></label>
                <select name="club" id="club" class="form-select">
                    <option value="1" <?php echo $club == 1 ? "selected" : "" ?>><?=__('فعال')?></option>
                    <option value="0" <?php echo $club == 0 ? "selected" : "" ?>><?=__('غیرفعال')?></option>
                </select>
            </div>
            <div class="col-12 text-center">
                <?php
                if ($action == "create") { ?>
                    <input type="hidden" id="id" value="0">
                    <button type="button" id="store-person" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                <?php
                } else if ($action == "update") {
                ?>
                    <input type="hidden" id="id" value="<?php echo $id; ?>">
                    <button type="button" id="store-person" data-action="update" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                <?php
                }
                ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas"><?=__('انصراف')?></button>
            </div>
        </form>
        <?php
    }

    public function showDebt()
    {
        $people = Person::where('balance', '<', 0)->get();
        if ($people->count() > 0) {
        ?>
            <table id="people-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>شماره عضویت</th>
                        <th>نام و نام خانوادگی</th>
                        <th>موبایل</th>
                        <th><?=__('مبلغ')?></th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($people as $person) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $person->id; ?></td>
                            <td><?php echo $person->getFullName(); ?></td>
                            <td><?php echo $person->mobile; ?></td>
                            <td><?php echo cnf($person->balance); ?></td>
                            <td>
                                <a href="<?php echo route('reportPerson', ['id' => $person->id]) ?>" class="btn btn-sm btn-info">گزارش حساب</a>
                                <?php
                                if (request()->user()->can('unlimited')) {
                                ?>
                                    <button class="btn btn-sm btn-warning remove-debt" data-id="<?php echo $person->id ?>" data-action="single">صفر کردن</button>
                                <?php
                                }
                                ?>
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
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
        <?php
        }
    }

    public static function birthdayCount()
    {
        $month = getMonth();
        $day = getDay();
        $day2=str_pad($day,2,0,STR_PAD_LEFT);
        $month2=str_pad($month,2,0,STR_PAD_LEFT);
        $people = Person::where('shamsi_birth', 'like', '____/' . $month . '/' . $day)
        ->orWhere('shamsi_birth', 'like', '____/' . $month2 . '/' . $day2)->count();
        return $people;
    }

    public function crudBirthday()
    {
        $title = "ارسال خودکار پیامک";
        $setting = new Setting;
        $days = $setting->getSetting('birthday_sms_days');
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <label class="form-label">پیامک تبریک، چند روز قبل از تولد شخص ارسال شود؟</label>
                    <input type="text" name="days" id="days" class="form-control just-numbers" placeholder="تعداد روز..." value="<?php echo $days ?>">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12 form-group">
                    <p>جهت ارسال در روز تولد، عدد 0 را وارد کنید و جهت غیر فعال سازی ارسال خودکار، این کادر را خالی بگذارید.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button type="button" id="store-birthday" class="btn btn-success me-sm-3 me-1">ذخیره تغییرات</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                </div>
            </div>
        </div>
<?php
    }
}
