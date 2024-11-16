<?php

namespace App\Models;

use App\Models\Admin\Menu;
use App\Models\User;
use App\Models\MyModels\Main;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivity extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'in', 'out'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showIndex($activities = null)
    {
        if ($activities == null) {
            $activities = UserActivity::latest()->get();
        }

        if($activities->count() > 0) {
            ?>
            <table id="activity-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نام کاربر</th>
                        <th>ورود</th>
                        <th>خروج</th>
                        <th>مدت زمان</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($activities as $activity) {
                    $a = UserActivity::where('id', $activity->id)->get();
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $activity->user?->getFullName(); ?></td>
                        <td><?php echo timeFormat($activity->in); ?></td>
                        <td><?php echo timeFormat($activity->out); ?></td>
                        <td><?php echo $this->minutes($a); ?> </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $activity->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-activity" data-id="<?php echo $activity->id; ?>">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                </tbody>
            </table>
            <div class="alert alert-warning my-3 text-center">
                <?php echo $this->minutes($activities) ?>
            </div>
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

    public function list()
    {
        $menu = Menu::where('url', 'user-activity')->first();
        if (auth()->user()->access == 1 || auth()->user()->group?->menus->contains($menu?->id)) {
            $activities = UserActivity::where('out', null)->latest()->get();
        } else {
            $activities = auth()->user()->activities->where('out', null);
        }
        if($activities->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نام کاربر</th>
                        <th>ورود</th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($activities as $activity) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $activity->user?->getFullName(); ?></td>
                        <td><?php echo timeFormat($activity->in); ?></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm store-activity-out" data-id="<?php echo $activity->id ?>">ثبت خروج</button>
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
        $title = "تردد پرسنل";
        $menu = Menu::where('url', 'user-activity')->first();
        if (auth()->user()->access == 1 || auth()->user()->group?->menus->contains($menu?->id)) {
            $users = User::getUsers();
        } else {
            $users = User::where('id', auth()->id())->get();
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <form id="user-activity-form">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12 form-group">
                        <label class="form-label">کاربر <span class="text-danger">*</span></label>
                        <select name="user_id" id="user-id" data-id="user-id" class="form-select select2-u checkEmpty">
                            <option value="">انتخاب</option>
                            <?php
                            foreach ($users as $user) {
                            ?>
                                <option value="<?php echo $user->id ?>"><?php echo $user->getFullName() ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback" data-id="user-id" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <button type="button" id="store-activity-in" class="btn btn-success me-sm-3 me-1">ثبت ورود</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
            <hr>
            <div id="activity-result"><?php $this->list() ?></div>
        </div>
        <?php
    }

    public function crud2($action, $id)
    {
        $users = User::getUsers();
        if ($action == "create") {
            $title = __('تردد جدید');
            $user_id = "";
            $in = "";
            $out = "";
        } else if ($action == "update") {
            $title = __("ویرایش تردد");
            $activity = UserActivity::find($id);
            $user_id = $activity->user_id;
            $in = timeFormat($activity->in);
            $out = timeFormat($activity->out);
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <form id="user-activity-form">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12 form-group">
                        <label class="form-label"><?=__('کاربر')?><span class="text-danger">*</span></label>
                        <select name="user_id" id="user-id" data-id="user-id" class="form-select select2-2 checkEmpty">
                            <option value=""><?=__('انتخاب')?></option>
                            <?php
                            foreach ($users as $user) {
                            ?>
                                <option value="<?php echo $user->id ?>" <?php echo $user_id == $user->id ? "selected" : "" ?>><?php echo $user->getFullName() ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback" data-id="user-id" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('زمان ورود')?> <span class="text-danger">*</span></label>
                        <input type="text" id="in" data-id="in" class="form-control checkEmpty datetime-mask" placeholder="1401/01/01 00:00" value="<?php echo $in ?>">
                        <div class="invalid-feedback" data-id="in" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('زمان خروج')?> <span class="text-danger">*</span></label>
                        <input type="text" id="out" data-id="out" class="form-control checkEmpty datetime-mask" placeholder="1401/01/01 00:00" value="<?php echo $out ?>">
                        <div class="invalid-feedback" data-id="out" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <button type="button" id="store-activity" class="btn btn-success me-sm-3 me-1 submit-by-enter" data-id="<?php echo $id ?>" data-action="<?php echo $action ?>"><?=__('ثبت اطلاعات')?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function minutes($activities)
    {
        $sum = 0;
        foreach ($activities as $activity) {
            $in = new DateTime($activity->in);
            $out = new DateTime($activity->out);
            $diff = $in->diff($out);

            $minutes = $diff->days * 24 * 60;
            $minutes += $diff->h * 60;
            $minutes += $diff->i;

            $sum += $minutes;
        }

        $hours = floor($sum / 60);
        $mins = $sum % 60;

        return $hours . " ساعت و " . $mins . " دقیقه";
    }
}
