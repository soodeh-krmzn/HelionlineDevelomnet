
<button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
<div class="modal-body">
    @if ($item)
        <div class="row">
            @php
                $doneStyle = 'background: yellow;width: 100%';
                $pastTime = $item->pastTime();
                $pastMinutes = $item->pastMinutes();
                $pastSeconds = $item->pastSeconds();
                $color = $pastMinutes < $item->min_duration ? 'text-white' : 'text-black';
            @endphp
            <div class="col-12 p-2 mb-4 counter" id="div" data-id="" data-bs-toggle="modal"
                data-bs-target="#crud">
                <div class="progress-bar text-center resizable-counter-item {{ $color }}">
                    <div id="ps" class="position-relative">
                        <div class="background-div  position-absolute h-100"
                            style="{{ $pastMinutes < $item->min_duration ? 'background:red' : $doneStyle }}">
                        </div>
                        <p class="position-relative"><b>{{ $item->title }}</b></p>
                        @if ($pastMinutes < $item->min_duration)
                            <div class="timer-div position-relative" data-duration="{{ $item->min_duration }}"
                                data-initial-minutes="{{ $pastMinutes }}" data-initial-seconds={{ $pastSeconds }}>
                                <span class="hour-container {{ $pastTime['hours'] == 0 ? 'd-none' : '' }}">
                                    <span class="hour">{{ $pastTime['hours'] }}</span> @lang('ساعت') @lang('و')
                                </span>
                                <span class="min-container {{ $pastTime['minutes'] == 0 ? 'd-none' : '' }}">
                                    <span class="min">{{ $pastTime['minutes'] }} </span> @lang('دقیقه') @lang('و')
                                </span>
                                <span class="sec-container {{ $pastTime['seconds'] == 0 ? 'd-none' : '' }}">
                                    <span class="sec">{{ $pastTime['seconds'] }}</span> @lang('ثانیه')
                                </span>
                            </div>
                        @else
                            <div class="position-relative">
                                <b>اتمام زمان</b>
                            </div>
                        @endif
                        <div class="mb-2 position-relative">
                            {{ __('از') }} <span id="counter-min">{{ $item->min_duration }}</span>
                            {{ __('دقیقه') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-0">
    @endif
    @if ($item)
        <form id="stock-form">
            <div class="text-center mb-0 mt-0 mt-md-n2">
                <h6 class="secondary-font"><?php echo $title; ?></h6>
            </div>
            <div class="row mb-3">
                <div class="col-md-12 form-group">
                    <label class="form-label"><?php echo __('مدت شمارنده'); ?> <span class="text-danger">*</span></label>
                    <input type="text" name="change" id="action-value" data-id="change"
                        class="form-control checkEmpty just-numbers" value="<?= $item->min_duration ?>"
                        placeholder="<?php echo __('مدت شمارنده'); ?>...">
                    <div class="invalid-feedback" data-id="change" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <button type="button" data-type='update' data-id="<?php echo $item->id; ?>"
                        class="btn btn-warning update-counter me-sm-3 me-1"><?php echo __('اعمال'); ?></button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                        aria-label="Close"><?php echo __('انصراف'); ?></button>
                </div>
            </div>
        </form>
    @else
    @php
    $counters=App\Models\Counter::all();

    @endphp
        <form id="stock-form">
            <div class="text-center mb-0 mt-0 mt-md-n2">
                <h6 class="secondary-font">@lang('اعمال شمارنده')</h6>
            </div>
            <div class="row mb-3">
                <div class="col-md-12 form-group">
                    <label class="form-label"><?php echo __('ثبت شمارنده'); ?> <span class="text-danger">*</span></label>
                    <select id="action-value" class="form-select">
                        @foreach ($counters as $counter)
                        <option value="{{$counter->id}}">{{$counter->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <button type="button" data-type='create' data-id="{{$id}}"
                        class="btn btn-warning update-counter me-sm-3 me-1"><?php echo __('اعمال'); ?></button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                        aria-label="Close"><?php echo __('انصراف'); ?></button>
                </div>
            </div>
        </form>
    @endif

</div>
<script>
    //    $step = 100 / $row->counter_min;
    //         if($row->counter_passed == 0) {
    //             $j = 0;
    //         } else {
    //             $j = $row->counter_passed * $step;
    //         }
    function fillRed(duration, pastSeconds, timerDiv) {
        //  var totalSeconds = duration ;
        let step = 100 / (duration * 60);
        let j = pastSeconds * step;
        $(timerDiv).parent().find('.background-div').css('width', j +
            "%");
    }

    function runTimer(timerDiv) {
        // Get the current time
        let pastTime = {
            hours: parseInt($(timerDiv).find('.hour').text()) || 0,
            minutes: parseInt($(timerDiv).find('.min').text()) || 0,
            seconds: parseInt($(timerDiv).find('.sec').text()) || 0,
            initialMinutes: parseInt($(timerDiv).attr('data-initial-minutes'))
        };
        let duration = parseInt($(timerDiv).attr('data-duration'));
        let pastSeconds = $(timerDiv).attr('data-initial-seconds');
        fillRed(duration, pastSeconds, timerDiv);
        let intervalId = setInterval(function() {
            pastTime.seconds++;
            pastSeconds++;
            fillRed(duration, pastSeconds, timerDiv);
            if (pastTime.seconds >= 60) {
                pastTime.seconds = 0;
                pastTime.minutes++;
                pastTime.initialMinutes++;
                if (pastTime.minutes >= 60) {

                    pastTime.minutes = 0;
                    pastTime.hours++;
                }
            }
            // console.log(pastTime.initialMinutes, duration);
            if (pastTime.initialMinutes >= duration) {
                clearInterval(intervalId);
                $(timerDiv).html('<div class="position-relative"><b>اتمام زمان</b></div>');
                $(timerDiv).parent().css("color", "black");
                $(timerDiv).parent().find('.background-div').css("background-color", "yellow");
            }
            // Update the DOM
            if (pastTime.hours > 0) {
                $(timerDiv).find('.hour-container').removeClass('d-none');
            }
            // console.log(pastTime.minutes,pastTime.minutes > 0);
            if (pastTime.minutes > 0) {
                $(timerDiv).find('.min-container').removeClass('d-none');
            }
            $(timerDiv).find('.hour').text(pastTime.hours);
            $(timerDiv).find('.min').text(pastTime.minutes);
            $(timerDiv).find('.sec').text(pastTime.seconds);
        }, 1000);

        // Return the interval ID for later reference
        return intervalId;
    }

    $('.timer-div').each(function() {
        // 'this' refers to the current element in the loop
        runTimer(this);
    });
</script>
