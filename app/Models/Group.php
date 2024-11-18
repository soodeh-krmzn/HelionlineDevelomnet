<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Syncable;

class Group extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $fillable = ['name', 'details'];

    public function people()
    {
        return $this->belongsToMany(Person::class, 'group_person', 'person_id', 'group_id');
    }

    public function showIndex()
    {
        $groups = Group::orderBy('name', 'desc')->get();
        if ($groups->count() > 0) {
?>
            <table id="group-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?php echo __('ردیف') ?></th>
                        <th><?php echo __('نام گروه') ?></th>
                        <th><?php echo __('توضیحات') ?></th>
                        <th><?php echo __('مدیریت') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($groups as $group) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $group->name; ?></td>
                            <td><?php echo $group->details; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $group->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-group" data-id="<?php echo $group->id; ?>"><i class="bx bx-trash"></i></button>
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
            $title = __('گروه جدید');
            $name = "";
            $details = "";
        } else if ($action == "update") {
            $title = __('ویرایش گروه');
            $group = Group::find($id);
            $name = $group->name;
            $details = $group->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?php echo __('نام گروه') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?php echo __('نام گروه') ?>..." value="<?php echo $name; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mt-2">
            </div>
            <div class="row mt-2">
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
                    <button type="button" id="store-group" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات') ?></button>
                <?php
                } else {
                ?>
                    <button type="button" id="store-group" data-id="<?php echo $id ?>" data-action="update" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات') ?></button>
                <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
            </div>
        </div>
    <?php
    }

    public function peopleForm()
    {
        $title = __('افزودن اشخاص');
        $people = Person::latest()->get();
    ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <button id="export-people" style="position: absolute;top: 14px;right: 11px;" class="btn btn-sm btn-success">
            <span>
                <i class="bx bxs-file-export"></i>
            </span>
        </button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row mt-2">
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?php echo __('اشخاص') ?></label>
                    <select id="people" class="form-select select2" multiple>
                        <?php
                        foreach ($people as $person) { ?>
                            <option value="<?php echo $person->id ?>" <?php echo $this->people->contains($person->id) ? 'selected' : '' ?>><?php echo $person->getFullName() . ' ( ' . $person->id . ' )' ?></option>
                        <?php
                        } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <button type="button" id="store-people" data-id="<?php echo $this->id ?>" class="btn btn-success me-sm-3 me-1"><?php echo __('ثبت اطلاعات') ?></button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف') ?></button>
            </div>
        </div>
    <?php
    }

    public function showPeople()
    {
    ?>
        <div class="row mx-1 my-3">
            <div class="col-12 mb-3">
                <div class="d-flex">
                    <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                        <input class="form-check-input menu" type="checkbox" id="check-all" checked>
                        <label class="form-check-label" for="check-all"><i class="menu-icon bx bx-list-check"></i> <?php echo __('انتخاب همه') ?></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                foreach ($this->people as $person) {
                ?>
                    <div class="col-3 mb-3">
                        <div class="d-flex">
                            <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                                <input class="form-check-input group-person g-person-check" type="checkbox" id="<?php echo $person->id ?>" name="people[]" value="<?php echo $person->id ?>" checked>
                                <label class="form-check-label" for="<?php echo $person->id ?>"><?php echo $person->getFullName() ?></label>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    }

    public function showCheckbox($groups)
    {
    ?>
        <div class="row">
            <div class="col-3 mb-4">
                <div class="d-flex">
                    <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                        <input class="form-check-input" type="checkbox" id="group-check-all" name="groups[]">
                        <label class="form-check-label" for="group-check-all"><?php echo __('همه') ?></label>
                    </div>
                </div>
            </div>
            <?php
            foreach ($groups as $group) { ?>
                <div class="col-3 mb-4">
                    <div class="d-flex">
                        <div class="form-check me-3 me-lg-5 mb-0 mt-0">
                            <input class="form-check-input group-check" type="checkbox" id="group-<?php echo $group->id ?>" name="groups[]" value="<?php echo $group->id ?>">
                            <label class="form-check-label" for="group-<?php echo $group->id ?>"><?php echo $group->name ?></label>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
<?php
    }
}
