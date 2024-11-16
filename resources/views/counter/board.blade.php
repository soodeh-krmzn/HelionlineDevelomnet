@extends('parts.zero-master')
@section('title', 'تابلوی شمارنده')
@section('head-styles')
    <style type="text/css">
        /* #result {
            resize: both;
            overflow: scroll;
        } */
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
                <div class="row" id="result">
                    {{ $counter->presentsInTimer($res) }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
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

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            function run_timer(id) {
                let time = $("#ps" + id).data("time") * 60;
                let passed = $("#ps" + id).data("passed");
                let step = 100 / time;

                let second_passed;
                let minute_passed;

                if (passed <= time) {
                    let j = passed * step;
                    passed++;
                    $("#ps" + id).css({"width": j + "%"});

                    $("#ps" + id).data("passed", passed);

                    minute_passed = Math.floor(passed / 60);
                    second_passed = Math.floor(passed % 60);

                    let label = "";

                    if (passed <= 60) {
                        label = second_passed + " ثانیه ";
                    } else {
                        label = minute_passed + " دقیقه و " + second_passed + " ثانیه ";
                    }
                    $("#sec" + id).html(label);

                    //console.log(passed + " for " + id + "\n");
                } else {
                    passed++;
                    $("#ps" + id).data("passed", passed);
                    $("#ps" + id).css(
                        {
                            "background": "yellow",
                            "color": "black"
                        }
                    );
                    $("#sec" + id).html("اتمام زمان");
                }


                if (passed % 60 == 0) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePassedTime') }}",
                        data:{
                            check_end_is_set: 1,
                            id: id,
                            passed: passed / 60
                        },
                        success: function(data) {
                            if (data.end == true) {
                                $("#div" + id).remove();
                            } else {
                                console.log(id);
                                console.log(data.min);
                                $(`#ps${id}`).data("time", data.min);
                                $(`#counter-min${id}`).html(data.min);
                            }
                        }
                    });
                }

            }

            setInterval(function() {
                // var ids = $(".ids").val();
                // var list = ids.split(',');
                var list = $(".t-ids").serializeArray();
                var i;
                for (i = 0; i < list.length; i++) {
                    run_timer(list[i].value);
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('newPresents') }}",
                    data:{
                        ids: $(".t-ids").serialize()
                    },
                    success: function(data) {
                        $("#result").append(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }, 1000);

            /*
            setInterval(function() {
                var back_url = $('#home-url').val() + "gt-include/script/timer.php";
                const ids_str = [];
                $(".ids").each(function() {
                    ids_str.push($(this).val());
                });
                $.post(back_url, {refresh_timer_list:1, ids:ids_str.toString()}, function(data) {
                    if (data) {
                        $("#result").append(data);
                    }
                });
            }, 1000);
            */

        });
    </script>
@stop
