<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class Question extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['vote_id', 'title', 'type', 'display_order'];

    public function responses()
    {
        return $this->hasMany(VoteResponse::class);
    }

    public function showIndex($vote_id)
    {
        $questions = Question::where('vote_id', $vote_id)->orderBy('display_order', 'desc')->get();
        if ($questions->count() > 0) {
?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?= __('ردیف') ?></th>
                        <th><?=__('عنوان')?></th>
                        <th><?=__('نوع')?></th>
                        <th><?=__('ترتیب نمایش')?></th>
                        <th><?=__('مدیریت')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($questions as $question) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $question->title; ?></td>
                            <td><span class="badge bg-<?php echo ($question->type == "test") ? 'info' : 'warning'; ?>"><?php echo ($question->type == "test") ? __('چند گزینه ای') : __('متنی'); ?></span></td>
                            <td><?php echo $question->display_order; ?></td>
                            <td>
                                <?php if (Gate::allows('update')) { ?>
                                    <button type="button" class="btn btn-warning btn-sm edit-question" data-title="<?php echo $question->title; ?>" data-type="<?php echo $question->type; ?>" data-display_order="<?php echo $question->display_order; ?>" data-id="<?php echo $question->id; ?>">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                <?php }
                                if (Gate::allows('delete')) { ?>
                                    <button type="button" class="btn btn-danger btn-sm delete-question" data-id="<?php echo $question->id; ?>"><i class="bx bx-trash"></i></button>
                                <?php } ?>
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
                    <div class="alert alert-danger text-center m-0"><?= __('موردی جهت نمایش موجود نیست.') ?></div>
                </div>
            </div>
        <?php
        }
    }

    public function crud($id, $vote_id)
    {
        $vote = Vote::find($vote_id);
        $_title = __('لیست سوالات') . '"' . $vote->name . '"';
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $_title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('عنوان سوال')?> <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" data-id="title" class="form-control checkEmpty" placeholder="<?=__('عنوان سوال')?>..." value="">
                    <div class="invalid-feedback" data-id="title" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('نوع سوال')?></label>
                    <select name="type" id="type" class="form-select">
                        <option value="test"><?=__('چند گزینه ای')?></option>
                        <option value="text"><?=__('متنی')?></option>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label"><?=__('ترتیب نمایش')?></label>
                    <select name="display_order" id="display-order" class="form-select">
                        <?php
                        for ($i = 1; $i <= 20; $i++) {
                        ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12 text-center">
                    <button type="button" id="store-question" data-action="create" data-id="<?php echo $id; ?>" data-vote_id="<?php echo $vote_id; ?>" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?= __('ثبت اطلاعات') ?></button>
                    <button type="reset" id="ignore-question" class="btn btn-label-secondary" aria-label="Close"><?= __('انصراف') ?></button>
                </div>
            </div>
            <hr>
            <div class="row mt-2">
                <div class="col-12"><?php $this->showIndex($vote_id); ?></div>
            </div>
        </div>
<?php
    }
}
