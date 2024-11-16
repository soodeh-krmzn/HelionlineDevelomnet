<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteResponse extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['question_id', 'mobile', 'answer'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        $question = $this->question;
        if ($question->type == "test") {
            switch ($this->answer) {
                case "1":
                    return "خیلی ضعیف";
                    break;
                case "2":
                    return "ضعیف";
                    break;
                case "3":
                    return "متوسط";
                    break;
                case "4":
                    return "خوب";
                    break;
                case "5":
                    return "عالی";
                    break;
                default:
                    return $this->answer;
            }
        } else {
            return $this->answer;
        }
    }

    public function showIndex($voteId)
    {
        $vote = Vote::find($voteId);
        if ($vote) {
            $questions = $vote->questions;
            if($questions->count() > 0) {
                foreach ($questions as $question) {
                    $responses = $question->responses()->latest()->get();
                    ?>
                    <div class="col-xl-6 col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <?php echo $question->title; ?>
                                </h4>
                            </div>
                            <div class="card-datatable table-responsive">
                                <div class="card-body">
                                <?php
                                if ($responses->count() > 0) {
                                ?>
                                    <table class="table table-hover border-top response-table">
                                        <thead>
                                            <tr>
                                                <th><?=__('ردیف')?></th>
                                                <th><?=__('پاسخ')?></th>
                                                <th><?=__('موبایل')?></th>
                                                <th><?=__('تاریخ')?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $i = 1;
                                        foreach($responses as $response) { ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $response->answer(); ?></td>
                                                <td><?php echo $response->mobile; ?></td>
                                                <td><?php echo timeFormat($response->created_at); ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php if ($question->type == "test") { ?>
                                        <div id="question-<?php echo $question->id ?>" data-id="<?php echo $question->id ?>" class="chart mt-4"></div>
                                    <?php }
                                } else {
                                ?>
                                    <div class="row mx-1">
                                        <div class="col-12">
                                            <div class="alert alert-danger text-center m-0"><?=__('پاسخی برای این سوال ثبت نشده است.')?></div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else { ?>
                <div class="row mx-1">
                    <div class="col-12">
                        <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                    </div>
                </div>
                <?php
            }
        } else {
        ?>
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?=__('یافت نشد')?>.</div>
                </div>
            </div>
        <?php
        }
    }
}
