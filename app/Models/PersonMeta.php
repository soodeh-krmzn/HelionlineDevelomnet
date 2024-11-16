<?php

namespace App\Models;

use App\Models\MyModels\Main;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonMeta extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'p_id', 'meta', 'value'];

    public function crud($id)
    {
        $title = __("اطلاعات تکمیلی شخص");
        $person = Person::find($id);
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <form id="meta-form">
                <h4><?=__('مشخصات پدر')?></h4>
                <div class="row mb-4">
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__("نام پدر")?> </label>
                        <input type="text" name="f-name" id="f-name" class="form-control" placeholder="<?=__("نام پدر")?>..." value="<?php echo $person->getMeta('f-name') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__("شغل پدر")?></label>
                        <input type="text" name="f-job" id="f-job" class="form-control" placeholder="<?=__("شغل پدر")?>..." value="<?php echo $person->getMeta('f-job') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__("تحصیلات پدر")?></label>
                        <input type="text" name="f-education" id="f-education" class="form-control" placeholder="<?=__("تحصیلات پدر")?>..." value="<?php echo $person->getMeta('f-education') ?>">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__("تاریخ تولد")?></label>
                        <input type="text" name="f-birth" id="f-birth" data-id="f-birth" class="form-control  date-mask" placeholder="1401/01/01..." value="<?php echo $person->getMeta('f-birth') ?>">
                        <div class="invalid-feedback" data-id="f-birth" data-error="checkDate"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('کدملی')?></label>
                        <input type="text" name="f-nationalcode" id="f-nationalcode" data-id="f-nationalcode" class="form-control just-numbers checkNationalCode" placeholder="<?=__('کدملی')?>..." value="<?php echo $person->getMeta('f-nationalcode') ?>" maxlength="10" onkeypress="return onlyNumberKey(event)">
                        <div class="invalid-feedback" data-id="f-nationalcode" data-error="checkNationalCode"></div>
                        </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('شماره موبایل')?></label>
                        <input type="text" name="f-mobile" id="f-mobile" data-id="f-mobile" class="form-control just-numbers checkMobile" placeholder="<?=__('شماره موبایل')?>..." value="<?php echo $person->getMeta('f-mobile') ?>" maxlength="11" onkeypress="return onlyNumberKey(event)">
                        <div class="invalid-feedback" data-id="f-mobile" data-error="checkMobile"></div>
                    </div>
                </div>
                <hr>
                <h4><?=__('مشخصات مادر')?></h4>
                <div class="row mb-4">
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('نام مادر')?> </label>
                        <input type="text" name="m-name" id="m-name" class="form-control" placeholder="<?=__('نام مادر')?>..." value="<?php echo $person->getMeta('m-name') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('شغل مادر')?></label>
                        <input type="text" name="m-job" id="m-job" class="form-control" placeholder="<?=__('شغل مادر')?>..." value="<?php echo $person->getMeta('m-job') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('تحصیلات مادر')?></label>
                        <input type="text" name="m-education" id="m-education" class="form-control" placeholder="<?=__('تحصیلات مادر')?>..." value="<?php echo $person->getMeta('m-education') ?>">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('تاریخ تولد')?></label>
                        <input type="text" name="m-birth" id="m-birth" data-id="m-birth" class="form-control  date-mask" placeholder="1401/01/01..." value="<?php echo $person->getMeta('m-birth') ?>">
                        <div class="invalid-feedback" data-id="m-birth" data-error="checkDate"></div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('کد ملی')?></label>
                        <input type="text" name="m-nationalcode" id="m-nationalcode" data-id="f-nationalcode" class="form-control just-numbers checkNationalCode" placeholder="<?=__('کد ملی')?>..." value="<?php echo $person->getMeta('m-nationalcode') ?>" maxlength="10" onkeypress="return onlyNumberKey(event)">
                        <div class="invalid-feedback" data-id="m-nationalcode" data-error="checkNationalCode"></div>
                        </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('شماره موبایل')?></label>
                        <input type="text" name="m-mobile" id="m-mobile" data-id="m-mobile" class="form-control just-numbers checkMobile" placeholder="<?=__('شماره موبایل')?>..." value="<?php echo $person->getMeta('m-mobile') ?>" maxlength="11" onkeypress="return onlyNumberKey(event)">
                        <div class="invalid-feedback" data-id="m-mobile" data-error="checkMobile"></div>
                    </div>
                </div>
                <hr>
                <h4><?=__('اطلاعات تماس')?></h4>
                <div class="row mb-4">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('آدرس منزل')?></label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="<?=__('آدرس منزل')?>..." value="<?php echo $person->getMeta('address') ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('تلفن منزل')?></label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="<?=__('تلفن منزل')?>..." value="<?php echo $person->getMeta('phone') ?>">
                    </div>
                </div>
                <hr>
                <div class="row mb-4">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('کودک شما با چه کسی زندگی میکند؟')?></label>
                        <input type="text" name="live-with" id="live-with" class="form-control" placeholder="<?=__('کودک شما با چه کسی زندگی میکند؟')?>" value="<?php echo $person->getMeta('live-with') ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('آیا کودک شما به خوراکی یا داروی خاصی حساسیت دارد؟')?></label>
                        <input type="text" name="alergy" id="alergy" class="form-control" placeholder="<?=__('آیا کودک شما به خوراکی یا داروی خاصی حساسیت دارد؟')?>" value="<?php echo $person->getMeta('alergy') ?>">
                    </div>
                </div>
                <hr>
                <h4><?=__('آشناها')?></h4>
                <div class="row mb-4">
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('نام آشنا')?> 1</label>
                        <input type="text" name="relative-name-1" id="relative-name-1" class="form-control" placeholder="<?=__('نام آشنا')?> 1..." value="<?php echo $person->getMeta('relative-name-1') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('نسبت آشنا')?> 1</label>
                        <input type="text" name="relative-connection-1" id="relative-connection-1" class="form-control" placeholder="<?=__('نسبت آشنا')?> 1..." value="<?php echo $person->getMeta('relative-connection-1') ?>">
                        </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('تلفن آشنا')?> 1</label>
                        <input type="text" name="relative-mobile-1" id="relative-mobile-1" class="form-control just-numbers" placeholder="<?=__('تلفن آشنا')?> 1..." value="<?php echo $person->getMeta('relative-mobile-1') ?>" onkeypress="return onlyNumberKey(event)">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('نام آشنا')?> 2</label>
                        <input type="text" name="relative-name-2" id="relative-name-2" class="form-control" placeholder="<?=__('نام آشنا')?> 2..." value="<?php echo $person->getMeta('relative-name-2') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('نسبت آشنا')?> 2</label>
                        <input type="text" name="relative-connection-2" id="relative-connection-2" class="form-control" placeholder="<?=__('نسبت آشنا')?> 2..." value="<?php echo $person->getMeta('relative-connection-2') ?>">
                        </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label required"><?=__('تلفن آشنا')?> 2</label>
                        <input type="text" name="relative-mobile-2" id="relative-mobile-2" class="form-control just-numbers" placeholder="<?=__('تلفن آشنا')?> 2..." value="<?php echo $person->getMeta('relative-mobile-2') ?>" onkeypress="return onlyNumberKey(event)">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" id="store-meta" data-id="<?php echo $id; ?>" class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function crudGame($p_id)
    {
        $person = Person::find($p_id);
        if (!$person) {
            return response()->json([
                'message' => "{{ __('متاسفانه خطایی رخ داده است.') }}"
            ], 404);
        }
        $info = $person->getMeta("person-info");
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo __('یادداشت پرسنل'); ?></h3>
            </div>
            <form id="person-info-form">
                <div class="row mb-4">
                    <div class="col-md-12 form-group" id="offer-price-field">
                        <label class="form-label"><?php echo __('یادداشت پرسنل'); ?></label>
                        <textarea name="person-info" id="person-info" class="form-control" placeholder="<?php echo __('یادداشت پرسنل'); ?>..."><?php echo $info ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" id="store-person-info" data-p_id="<?php echo $p_id ?>" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات'); ?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?php echo __('انصراف'); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

}
