<?php

namespace App\Models;

use App\Models\Admin\Menu;
use App\Models\MyModels\Main;
use App\Services\Database;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function menus()
    {
        $db = (new Database())->dbName();
        return $this->belongsToMany(Menu::class, $db . ".menu_user_group", "group_id", "menu_id");
    }

    public function hasPermission(string $permission) : bool
    {
        $check = UserGroupPermission::where('group_id', $this->id)
                    ->where('permission', $permission)
                    ->count();
        return $check > 0;
    }

    public function permissions()
    {
        return $this->hasMany(UserGroupPermission::class, 'group_id');
    }

    public function showIndex()
    {
        $groups = UserGroup::orderBy('name', 'desc')->get();
        if($groups->count() > 0) {
            ?>
            <table id="group-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>نقش</th>
                        <th><?=__('توضیحات')?></th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($groups as $group) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $group->name; ?></td>
                        <td><?php echo $group->description; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $group->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-group" data-id="<?php echo $group->id; ?>"><i class="bx bx-trash"></i></button>
                            <button type="button" class="btn btn-info btn-sm menu-group" data-id="<?php echo $group->id; ?>" data-bs-target="#menu" data-bs-toggle="modal"><i class="bx bx-menu"></i></button>
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
            $title = __("نقش جدید");
            $group = new UserGroup;
            $name = "";
            $details = "";
        } else if ($action == "update") {
            $title = __("ویرایش نقش");
            $group = UserGroup::find($id);
            $name = $group->name;
            $details = $group->description;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('نام')?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('نام')?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mt-2">
                <label class="form-label required"><?=__('قابلیت ها')?></label>
                <div class="col-4 form-group">
                    <input class="form-check-input permission" type="checkbox" id="create" name="create" <?php echo $group->hasPermission("create") ? "checked" : "" ?>>
                    <label class="form-check-label" for="create"><?=__('ایجاد')?></label>
                </div>
                <div class="col-4 form-group">
                    <input class="form-check-input permission" type="checkbox" id="delete" name="delete" <?php echo $group->hasPermission("delete") ? "checked" : "" ?>>
                    <label class="form-check-label" for="delete"><?=__('حذف')?></label>
                </div>
                <div class="col-4 form-group" style="white-space: nowrap;">
                    <input class="form-check-input permission" type="checkbox" id="update" name="update" <?php echo $group->hasPermission("update") ? "checked" : "" ?>>
                    <label class="form-check-label" for="update"><?=__('ویرایش')?></label>
                </div>
            </div>
            <div class="row mt-2">
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
                    <button type="button" id="store-group" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                    <?php
                } else {
                    ?>
                    <button type="button" id="store-group" data-id="<?php echo $id ?>" data-action="update" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                    <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
            </div>
        </div>
        <?php
    }

    public function showMenus($id)
    {
        $title = __("تخصیص منو");
        $group = UserGroup::find($id);
        $check_all = "";
        if ($group->menus?->count() == Menu::where('display_nav', 1)->count()) {
            $check_all = "checked";
        }
        $menus = Menu::where('parent_id', 0)->where('display_nav', 1)->orderBy('display_order')->get();
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <div class="d-flex">
                        <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                            <input class="form-check-input menu" type="checkbox" id="check-all" <?php echo $check_all; echo $group->menus?->count(); echo Menu::where('display_nav', 1)->count() ?>>
                            <label class="form-check-label" for="check-all"><i class="menu-icon bx bx-list-check"></i> <?=__('انتخاب همه')?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                foreach ($menus as $menu) {
                ?>
                <div class="col-lg-6 mb-4">
                    <div class="d-flex">
                        <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                            <input class="form-check-input menu menu-check" type="checkbox" id="<?php echo $menu->id ?>" name="menus[]" value="<?php echo $menu->id ?>" <?php echo $group->menus->contains($menu->id) ? "checked" : "" ?>>
                            <label class="form-check-label" for="<?php echo $menu->id ?>"><i class="menu-icon bx bx-<?php echo $menu->icon ?>"></i> <?php echo $menu->name ?></label>
                        </div>
                    </div>
                    <div>
                        <?php $group->subMenus($menu->id) ?>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <button type="button" id="store-group-menus" data-id="<?php echo $id ?>" class="btn btn-success me-sm-3 me-1"><?=__('ثبت اطلاعات')?></button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
            </div>
        </div>
        <?php
    }

    public function subMenus($menu_id)
    {
        $menus = Menu::where('parent_id', $menu_id)->where('display_nav', 1)->orderBy('display_order')->get();
        if ($menus->count() > 0) {
            ?>
            <ul class="pr-5">
                <?php
                foreach ($menus as $menu) {
                    ?>
                    <li>
                        <div class="d-flex">
                            <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                                <input class="form-check-input menu menu-check" type="checkbox" id="<?php echo $menu->id ?>" name="menus[]" value="<?php echo $menu->id ?>" <?php echo $this->menus->contains($menu->id) ? "checked" : "" ?>>
                                <label class="form-check-label" for="<?php echo $menu->id ?>"><i class="menu-icon bx bx-<?php echo $menu->icon ?>"></i> <?php echo $menu->name ?></label>
                            </div>
                        </div>
                        <div>
                            <?php $this->subMenus($menu->id) ?>
                        </div>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
    }
}
