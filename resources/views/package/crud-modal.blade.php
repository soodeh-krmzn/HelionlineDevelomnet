@php
use App\Models\Package;
    if ($action == 'create') {
        $title = __('بسته جدید');
        $name = '';
        $price = '';
        $expire_time = '';
        $expire_day = '';
        $type='';
    } elseif ($action == 'update') {
        $title = __('ویرایش بسته');
        $package = Package::find($id);
        $name = $package->name;
        $price = $package->price;
        $expire_time = $package->expire_time;
        $expire_day = $package->expire_day;
        $type=$package->type;
    }
@endphp
<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body">
    <div class="text-center mb-4 mt-0 mt-md-n2">
        <h3 class="secondary-font"><?php echo $title; ?></h3>
    </div>
    <div class="row mb-4">
        <div class="col-md-6 form-group">
            <label class="form-label required"><?php echo __('نام بسته '); ?> <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" data-id="name" class="form-control checkEmpty"
                placeholder="<?php echo __('نام بسته '); ?>..." value="<?php echo $name; ?>">
            <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
        </div>
        <div class="col-md-6 form-group">
            <label class="form-label required"><?php echo __('هزینه'); ?> <span class="text-danger">*</span></label>
            <input type="text" name="price" data-id="price"
                class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('هزینه'); ?>..."
                value="<?php echo $price != '' ? cnf($price) : ''; ?>">
            <input type="hidden" name="price" id="price" data-id="price"
                class="form-control just-numbers checkEmpty" value="<?php echo $price; ?>">
            <div class="invalid-feedback" data-id="price" data-error="checkEmpty"></div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="form-group">
                <label class="form-label">نوع بسته</label>
                <select name="type" class="form-select" id="type">
                    <option value="min">@lang('دقیقه ای')</option>
                    <option value="times">@lang('مرتبه ای')</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6 form-group type-input ">
            <label class="form-label required">{{$type!='times'? __('شارژ به دقیقه'): __('تعداد مرتبه')}}<span class="text-danger">*</span></label>
            <input type="text" name="expire_time" data-id="expire-time"
                class="form-control just-numbers money-filter checkEmpty" placeholder="{{$type!='times'? __('شارژ به دقیقه'): __('تعداد مرتبه')}}..."
                value="<?php echo $expire_time != '' ? cnf($expire_time) : ''; ?>">
            <input type="hidden" name="expire_time" id="expire-time" data-id="expire-time"
                class="form-control just-numbers checkEmpty" value="<?php echo $expire_time; ?>">
            <div class="invalid-feedback" data-id="expire-time" data-error="checkEmpty"></div>
        </div>
        <div class="col-md-6 form-group">
            <label class="form-label required"><?php echo __('شارژ به روز'); ?> <span class="text-danger">*</span></label>
            <input type="text" name="expire_day" data-id="expire-day"
                class="form-control just-numbers money-filter checkEmpty" placeholder="<?php echo __('شارژ به روز'); ?>..."
                value="<?php echo $expire_day != '' ? cnf($expire_day) : ''; ?>">
            <input type="hidden" name="expire_day" id="expire-day" data-id="expire-day"
                class="form-control just-numbers checkEmpty" value="<?php echo $expire_day; ?>">
            <div class="invalid-feedback" data-id="expire-day" data-error="checkEmpty"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <?php
            if ($action == "create") { ?>
            <button type="button" id="store-package" data-action="create" data-id="0"
                class="btn btn-success me-sm-3 me-1 submit-by-enter"><?php echo __('ثبت اطلاعات'); ?></button>
            <?php
            } else {
                ?>
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
            <button type="button" id="store-package" data-action="update" data-id="<?php echo $id; ?>"
                class="btn btn-warning me-sm-3 me-1 submit-by-enter"><?php echo __('ویرایش اطلاعات'); ?></button>
            <?php
            } ?>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                aria-label="Close"><?php echo __('انصراف'); ?></button>
        </div>
    </div>
</div>
<script>
$('#type').on('change',function(){
    if($(this).val()=='min'){
        $('.type-input').find('label').html("{{__('شارژ به دقیقه')}}"+'<span class="text-danger">*</span>');
        $('.type-input').find('input[type=text]').attr('placeholder',"{{__('شارژ به دقیقه')}}");
    }else{
        $('.type-input').find('label').html("{{__('تعداد مرتبه')}}"+'<span class="text-danger">*</span>');
        $('.type-input').find('input[type=text]').attr('placeholder',"{{__('تعداد مرتبه')}}");
    }
});
</script>
