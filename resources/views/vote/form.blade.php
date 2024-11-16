@extends('parts.zero-master')

@section('title', 'نظرسنجی')

@section('head-styles')
    <style type="text/css">
        .vote-icon {
            width: 40px;
            height: 40px;
            margin: 20px;
            transition: 0.2s;
            -webkit-transition: 0.2s;
            -moz-transition: 0.2s;
            -o-transition: 0.2s;
            border: 3px solid #fff;
            border-radius: 50%;
        }
        .vote-icon:hover {
            transform: scale(1.5);
            cursor: pointer;
            transition: 0.2s;
            -webkit-transition: 0.2s;
            -moz-transition: 0.2s;
            -o-transition: 0.2s;
        }
        .active-vote {
            border: 3px solid #fffb00;
            transform: scale(1.5);
            cursor: pointer;
            transition: 0.2s;
            -webkit-transition: 0.2s;
            -moz-transition: 0.2s;
            -o-transition: 0.2s;
            box-shadow: 0px 0px 10px #ccc;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">{{ $vote->voteForm() }}</div>
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

            $(".vote-icon").click(function() {
                var value = $(this).data("val");
                var q_id = $(this).data("q_id");
                $(`input[data-q_id=${q_id}]`).val(value);
                $(`.vote-icon[data-q_id=${q_id}]`).removeClass("active-vote");
                $(this).addClass("active-vote");
            });

            $(document.body).on("click", "#store-response", function() {
                var e = checkEmpty();
                var m = checkMobile();
                if (!e && !m) {
                    var mobile = $("#mobile").val();
                    var answers = $(".answer").serialize();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeResponse', $account) }}",
                        data: {
                            mobile: mobile,
                            answers: answers
                        },
                        success: function(data) {
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "با تشکر از همکاری شما. پاسخ شما با موفقیت ثبت شد.",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            Swal.fire({
                                title: "@lang('خطا')",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: "@lang('خطا')",
                        text: "لطفا به تمامی سوالات به درستی پاسخ دهید.",
                        icon: "error"
                    });
                }
            });

        });
    </script>
@stop
