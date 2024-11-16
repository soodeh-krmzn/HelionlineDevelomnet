<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Admin\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $connection = 'admindb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'family',
        'mobile',
        'username',
        'password',
        'group_id',
        'access',
        'status',
        'otp_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function group():BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->family;
    }

    public static function getUsers()
    {
        return User::where('account_id', auth()->user()->account_id)->latest()->get();
    }

    public static function getFirstUser()
    {
        return User::where('account_id', auth()->user()->account_id)->first();
    }

    public function showIndex()
    {
        $users = User::getUsers();
        if($users->count() > 0) {
            ?>
            <table id="user-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>نام کاربری</th>
                        <th>نقش</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($users as $user) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $user->name; ?></td>
                        <td><?php echo $user->family; ?></td>
                        <td><?php echo $user->username; ?></td>
                        <td><?php echo $user->group?->name; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $user->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-user" data-id="<?php echo $user->id; ?>">
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

    public function crud($action, $id)
    {
        $groups = UserGroup::all();
        if ($action == "create") {
            $user = new User;
            $title = __("کاربر جدید");
            $name = "";
            $family = "";
            $mobile = "";
            $group_id = "";
            $username = "";
            $access = 0;
        } else if ($action == "update") {
            $user = User::find($id);
            $title = __("ویرایش کاربر");
            $name = $user->name;
            $family = $user->family;
            $mobile = $user->mobile;
            $group_id = $user->group_id;
            $username = $user->username;
            $access = $user->access;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-6 form-group">
                    <label class="form-label"><?=__('نام')?><span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('نام')?>..." value="<?php echo $name ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-6 form-group">
                    <label class="form-label"><?=__('نام خانوادگی')?><span class="text-danger">*</span></label>
                    <input type="text" name="family" id="family" data-id="family" class="form-control checkEmpty" placeholder="<?=__('نام خانوادگی')?>..." value="<?php echo $family ?>">
                    <div class="invalid-feedback" data-id="family" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-6 form-group">
                    <label class="form-label"><?=__('موبایل')?><span class="text-danger">*</span></label>
                    <input type="text" name="mobile" id="mobile" data-id="mobile" class="form-control checkEmpty checkMobile just-numbers" placeholder="<?=__('موبایل')?>..." value="<?php echo $mobile ?>" maxlength="11">
                    <div class="invalid-feedback" data-id="mobile" data-error="checkEmpty"></div>
                    <div class="invalid-feedback" data-id="mobile" data-error="checkMobile"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('انتخاب نقش')?><span class="text-danger">*</span></label>
                    <select name="group-id" id="group-id" data-id="group-id" class="form-select checkEmpty">
                        <option value=""><?=__('هیچکدام')?></option>
                        <?php
                        foreach ($groups as $group) {
                        ?>
                            <option value="<?php echo $group->id ?>" <?php echo $group->id == $group_id ? "selected" : "" ?>><?php echo $group->name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback" data-id="group-id" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?=__('دسترسی')?></label>
                    <select name="access" id="access" class="form-select">
                        <option value="0" <?php echo $access == 0 ? "selected" : "" ?>><?=__('محدود')?></option>
                        <option value="1" <?php echo $access == 1 ? "selected" : "" ?>><?=__('نامحدود')?></option>
                        <option value="2" <?php echo $access == 2 ? "selected" : "" ?>><?=__('غیرفعال')?></option>
                    </select>
                </div>
            <?php
            if ($action == "create") {
            ?>
                <div class="col-6 form-group">
                    <label class="form-label"><?=__('رمز عبور')?><span class="text-danger">*</span></label>
                    <input type="text" name="s-password" id="s-password" data-id="s-password" class="form-control checkEmpty" placeholder="<?=__('رمز عبور')?>..." value="">
                    <div class="invalid-feedback" data-id="s-password" data-error="checkEmpty"></div>
                </div>
                <?php
            }
            ?>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                <?php
                    if ($action == "create") { ?>
                        <button type="button" id="store-user" data-action="create" data-id="0" class="btn btn-success me-sm-3 me-1 submit-by-enters"><?=__('ثبت اطلاعات')?></button>
                        <?php
                    } else if($action == "update") {
                        ?>
                        <button type="button" id="store-user" data-action="update" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                        <?php
                    } ?>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                </div>
            </div>
        </div>
        <?php
    }

}
