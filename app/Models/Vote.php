<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'details', 'status'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getStatus()
    {
        switch ($this->status) {
            case "1":
                return '<span class="badge bg-success">'.__('فعال').'</span>';
                break;
            default:
                return '<span class="badge bg-danger">'.__('غیرفعال').'</span>';
        }
    }

    public function showIndex()
    {
        $votes = Vote::orderBy('name', 'desc')->get();
        if($votes->count() > 0) {
            ?>
            <table class="table table-hover border-top">
                <thead>
                <tr>
                    <th><?=__('ردیف')?></th>
                    <th>عنوان</th>
                    <th><?=__('توضیحات')?></th>
                    <th><?=__('وضعیت')?></th>
                    <th>مدیریت</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach($votes as $vote) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $vote->name; ?></td>
                        <td><?php echo $vote->details; ?></td>
                        <td><span class="badge bg-<?php echo ($vote->status == 1) ? 'success' : 'danger'; ?>"><?php echo ($vote->status == 1) ? 'فعال' : 'غیرفعال'; ?></span></td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm crud-questions" data-vote_id="<?php echo $vote->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-question"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $vote->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-vote" data-id="<?php echo $vote->id; ?>"><i class="bx bx-trash"></i></button>
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

    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $title = __("نظرسنجی جدید");
            $name = "";
            $details = "";
            $status = "";
        } else if ($action == "update") {
            $title = __("ویرایش نظرسنجی");
            $vote = Vote::find($id);
            $name = $vote->name;
            $details = $vote->details;
            $status = $vote->status;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('عنوان نظرسنجی')?> <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty" placeholder="<?=__('عنوان نظرسنجی')?>..." value="<?php echo $name; ?>">
                    <div class="invalid-Vote" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('توضیحات')?></label>
                    <input type="text" name="details" id="details" class="form-control" placeholder="<?=__('توضیحات')?>..." value="<?php echo $details; ?>">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label"><?=__('وضعیت')?></label>
                    <select name="status" id="status" class="form-select">
                        <option <?php echo ($status == 0) ? 'selected' : ''; ?> value="0"><?=__('غیرفعال')?></option>
                        <option <?php echo ($status == 1) ? 'selected' : ''; ?> value="1" ><?=__('فعال')?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <?php
                if ($action == "create") { ?>
                    <button type="button" id="store-vote" data-action="create" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                    <?php
                } else {
                    ?>
                    <button type="button" id="store-vote" data-id="<?php echo $id ?>" data-action="update" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ویرایش اطلاعات')?></button>
                    <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
            </div>
        </div>
        <?php
    }

    public function voteForm()
    {
        $vote = Vote::where('status', 1)->first();
        if ($vote) {
            $questions = Question::where('vote_id', $vote->id)->orderBy('display_order', 'asc')->get();
            if ($questions->count() > 0) {
                $i = 1;
                foreach ($questions as $question) { ?>
                    <div class="card m-3 text-center">
                        <div class="card-header">
                            <h3 class="card-title m-0"><?php echo $question->title; ?></h3>
                        </div>
                        <div class="card-body">
                            <?php
                            if ($question->type == "test") {
                                ?>
                                <img class="vote-icon" src="<?php echo asset('assets/img/icons/vote/1.png') ?>" data-val="1" data-q_id="<?php echo $question->id ?>">
                                <img class="vote-icon" src="<?php echo asset('assets/img/icons/vote/2.png') ?>" data-val="2" data-q_id="<?php echo $question->id ?>">
                                <img class="vote-icon" src="<?php echo asset('assets/img/icons/vote/3.png') ?>" data-val="3" data-q_id="<?php echo $question->id ?>">
                                <img class="vote-icon" src="<?php echo asset('assets/img/icons/vote/4.png') ?>" data-val="4" data-q_id="<?php echo $question->id ?>">
                                <img class="vote-icon" src="<?php echo asset('assets/img/icons/vote/5.png') ?>" data-val="5" data-q_id="<?php echo $question->id ?>">
                                <input type="hidden" class="checkEmpty answer" name="<?php echo $question->id ?>" data-q_id="<?php echo $question->id ?>">
                                <?php
                            } else if ($question->type == "text") {
                                ?>
                                <label class="form-label">نظر شما</label>
                                <textarea class="form-control checkEmpty answer" data-id="mobile" name="<?php echo $question->id ?>" data-q_id="<?php echo $question->id ?>" placeholder="نظر شما..."></textarea>
                                <?php
                            } ?>
                        </div>
                    </div>
                    <?php
                    $i++;
                }
                ?>
                <div class="card m-3 text-center">
                    <div class="card-header">
                        <h3 class="card-title m-0">
                            شماره موبایل <small>(در صورت تمایل)</small>
                        </h3>
                    </div>
                    <div class="card-body">
                        <input type="text" id="mobile" class="form-control just-numbers checkMobile" placeholder="شماره موبایل...">
                        <div class="invalid-feedback" data-id="mobile" data-error="checkMobile"></div>
                    </div>
                </div>
                <div class="row mx-0">
                    <div class="col-md-12 text-center">
                        <button id="store-response" class="btn btn-success btn-lg form-control">ارسال نظر</button>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="row mx-1">
                    <div class="col-12">
                        <div class="alert alert-danger text-center m-0">هیچ سوالی موجود نیست.</div>
                    </div>
                </div>
                <?php
            }
        } else { ?>
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0">هیچ نظرسنجی فعالی در حال حاضر موجود نیست.</div>
                </div>
            </div>
            <?php
        }
    }

}
