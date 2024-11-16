@extends('parts.zero-master')
@section('title', __('تابلوی شمارنده'))
@section('head-styles')
    <style type="text/css">
        .resizable-counter-item {
            overflow-wrap: break-word;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div>
            <button id="zoom-in" class="btn btn-warning">+</button>
            <button id="zoom-out" class="btn btn-warning">-</button>
        </div>
        <div class="row">
            <div class="col-12 mt-4">
                <div class="row" id="result-v2">
                    @php
                        $doneStyle = 'background: yellow;width: 100%';
                    @endphp
                    @foreach ($counterItems as $item)
                        @php
                            $pastTime = $item->pastTime();
                            $pastMinutes = $item->pastMinutes();
                            $pastSeconds = $item->pastSeconds();
                            $color = $pastMinutes < $item->min_duration ? 'text-white' : 'text-black';
                        @endphp
                        <div class="col-3 p-2 mb-4 counter" id="div" data-id="" data-bs-toggle="modal"
                            data-bs-target="#crud">
                            <div class="progress-bar text-center resizable-counter-item {{ $color }}">
                                <div id="ps" class="position-relative">
                                    <button data-id="{{ $item->id }}"
                                        class="remove-counter btn btn-danger p-1 position-absolute cursor-pointer"
                                        style="z-index:1000;top:36%;left:2%">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                    <div class="background-div  position-absolute h-100"
                                        style="{{ $pastMinutes < $item->min_duration ? 'background:red' : $doneStyle }}">
                                    </div>
                                    <p class="position-relative"><b>{{ $item->title }}</b></p>
                                    @if ($pastMinutes < $item->min_duration)
                                        <div class="timer-div position-relative" data-duration="{{ $item->min_duration }}"
                                            data-initial-minutes="{{ $pastMinutes }}"
                                            data-initial-seconds={{ $pastSeconds }}>
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
                                            <b>@lang('اتمام زمان')</b>
                                        </div>
                                    @endif
                                    <div class="mb-2 position-relative">
                                        {{ __('از') }} <span id="counter-min">{{ $item->min_duration }}</span>
                                        {{ __('دقیقه') }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{-- {{ $counter->presentsInTimer($res) }} --}}
                </div>
            </div>
        </div>
    </div>

@stop

@section('footer-scripts')
    <script>
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
                    $(timerDiv).html('<div class="position-relative"><b>@lang('اتمام زمان')</b></div>');
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
    <script src="{{ asset('assets/js/RMain.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on("click", "#zoom-in", function() {
                var font_size = parseInt($(".resizable-counter-item").css("font-size"));
                if (font_size <= 40) {
                    var new_font_size = font_size + 5
                } else {
                    var new_font_size = font_size
                }
                $(".resizable-counter-item").css("font-size", new_font_size + "px");
            });

            $(document.body).on("click", "#zoom-out", function() {
                var font_size = parseInt($(".resizable-counter-item").css("font-size"));
                if (font_size >= 20) {
                    var new_font_size = font_size - 5
                } else {
                    var new_font_size = font_size
                }
                $(".resizable-counter-item").css("font-size", new_font_size + "px");
            });

            // check for refresh per minutes
            var refreshIntervalId = setInterval(function() {
                if (document.visibilityState === 'visible') {
                    actionAjax(window.location.href, {
                        last_update: "{{ $last_update }}",
                        type: 'check'
                    }, null, null, function(response) {
                        if (response.order == 'refresh') {
                            window.location.href = window.location.href;
                        }
                    });
                }
            }, 30000);
        });
        //listen and reload when the visibilty of page changed
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        });
        // remove a counter
        window.confirmTitle = "{{ __('اطمینان دارید؟') }}";
        window.confirmButtonText = "{{ __('بله، مطمئنم.') }}";
        window.cancelButtonText = "{{ __('نه، پشیمون شدم.') }}";
        $('.remove-counter').on('click', function() {
            var id = $(this).data('id');
            confirmAction("@lang('درحال حذف شمارنده')", function() {
                actionAjax(window.location.href, {
                    id: id,
                    type: 'remove'
                },false,false,function(){
                    location.reload();
                });
            });
        });
    </script>

@stop
