@extends('parts.master')

@section('title', __('هلی آنلاین'))

@section('head-styles')
    <style>
        html.dark-style table.checkout-table *,
        html.dark-style .shop-table thead * {
            color: black !important;
        }

        html.dark-style #deposit-type option,
        html.dark-style #deposit-type {
            color: rgb(255, 255, 255) !important;
        }

        html.dark-style input.money-filter {
            color: rgb(250, 250, 250) !important;
        }

        #payment-methods-table th {
            font-size: initial;
        }

        @media print {
            #bill-print-area table * {
                font-size: 3vw !important;
            }

            #bill-print-area table {
                min-width: initial !important;
            }

            #bill-print-area {
                width: 100% !important;
            }
        }

        @media (max-width: 1000px) {
            #game-modal .modal-dialog {
                min-width: 70%;
            }

            #bill-print-area div.table-responsive {
                overflow-x: initial !important;
            }

            #bill-print-area table {
                table-layout: auto;
                min-width: 700px;

            }

            #bill-print-area div.row.mx-0.mb-3.no-print {
                min-width: 700px;
            }

            #meta-result table table {
                table-layout: auto;
                min-width: 600px;
            }
        }
    </style>
@stop

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        @if ((new \App\Models\Setting())->getSetting('setup') == 'done')
            <div class="row g-4 mb-4">
                <div class="col">
                    @if ($notification = (new \App\Models\Admin\Option())->get_option('notification-' . app()->getLocale()))
                        <div class="card mb-4">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-danger m-2 text-center">{{ $notification }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col mx-2 my-2">
                                    <button class="btn btn-success btn-lg w-100 inactive" id="crud-game"
                                        data-bs-toggle="modal" data-bs-target="#crud">
                                        <i class="bx bx-log-in"></i>
                                        <span>&nbsp;{{ __('messages.entrance') }}&nbsp;</span>
                                    </button>
                                </div>
                                <div class="col mx-2 my-2">
                                    <button data-bs-toggle="modal" data-bs-target="#factor-modal"
                                        class="btn btn-warning btn-lg crud-factor w-100" data-f_type="nogame">
                                        <i class="bx bx-coffee"></i>
                                        <span>&nbsp{{ __('بوفه') }}&nbsp</span>
                                    </button>
                                </div>
                                <div class="col mx-2 my-2">
                                    <a href="{{ route('counterBoard') }}" target="_blank" class="btn btn-info btn-lg w-100">
                                        <i class="bx bx-time"></i>
                                        <span>&nbsp{{ __('شمارنده') }}&nbsp</span>
                                    </a>
                                </div>

                                {{-- all exept پایه و basic --}}
                                @if (!in_array(auth()->user()->account->package->id, [1, 10]))
                                    <div class="col mx-2 my-2">
                                        <button class="btn btn-primary btn-lg w-100" id="crud-user-activity"
                                            data-bs-toggle="modal" data-bs-target="#user-activity-modal">
                                            <i class="bx bx-user"></i>
                                            <span>&nbsp{{ __('پرسنل') }}&nbsp</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="col mx-2 my-2">
                                    <a href="https://helisoft.ir/musics/" target="_blank" class="btn btn-info btn-lg w-100">
                                        <i class="bx bx-music"></i>
                                        <span>&nbsp{{ __('پخش آهنگ') }}&nbsp</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users List Table -->
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-datatable table-responsive">
                            <div class="row mx-1 my-3">
                                <div class="col">
                                    <h5 class="card-title d-flex m-0">
                                        {{ __('حاضرین') }} <span class="mx-2" id="attendees"></span>
                                        @php
                                            $sections = (new \App\Models\Section())->getSelect();
                                        @endphp
                                        @if ($sections->count() > 0)
                                            <select id="section-filter" class="form-select ms-3">
                                                <option value="0">@lang('همه')</option>
                                                @foreach ($sections as $section)
                                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </h5>
                                </div>
                                <div class="col">
                                    <div
                                        class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                        <div class="dt-buttons btn-group flex-wrap">
                                            <div class="btn-group">
                                                <button id="toggle-checkbox" class="btn btn-info me-1" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#show-groups"
                                                    aria-expanded="false"
                                                    aria-controls="show-groups">{{ __('انتخاب') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 collapse my-1" id="show-groups">
                                    <div class="row">
                                        <div class="col-10" id="group-checkbox"></div>
                                        <div class="col-2">
                                            <div
                                                class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                                <div class="dt-buttons btn-group flex-wrap">
                                                    <div class="btn-group">
                                                        <button id="group-close" class="btn btn-danger me-1"
                                                            type="button">{{ __('ثبت خروج') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-1" id="game-result"></div>
                        </div>
                    </div>
                </div>
                @if ((new \App\Models\Setting())->getSetting('load_game_type') == 'page')
                    <div class="col">
                        <div class="card">
                            <div class="row mx-1" id="load-game-result"></div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="row g-4 mb-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="alert alert-warning text-center m-0">
                                        <h4 class="mb-3">{{ __('سلام. به هلی آنلاین خوش اومدین :)') }}</h4>
                                        <p class="m-0">
                                            {{ __('برای شروع کار وارد صفحه راه اندازی اولیه بشید، یک سری نکات اولیه رو اونجا بهتون توضیح دادیم.') }}
                                            <br>
                                            <a href="/setup"
                                                class="btn btn-success mt-3">{{ __('راه اندازی نرم افزار') }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!--/ Content -->

    <div class="modal fade" id="crud" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div id="crud-result"></div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="offer_modal1334" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div id="offer-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="person-info-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div id="person-info-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="factor-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content p-3 p-md-5">
                <div id="factor-result"></div>
            </div>
        </div>
    </div>

    @if (
        (new \App\Models\Setting())->getSetting('load_game_type') == 'modal' ||
            (new \App\Models\Setting())->getSetting('load_game_type') == '')
        <div class="modal fade" id="game-modal" tabindex="-1" style="direction: rtl" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content p-3">
                    <div id="load-game-result">

                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" style="z-index: 8000;" tabindex="-1" aria-labelledby="exampleModalCenterTitle"
        aria-modal="true" role="dialog" id="edit-meta-modal" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content p-3 p-md-5">
                <div id="edit-meta-result">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changes-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3">
                <div id="changes-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="user-activity-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3">
                <div id="crud-user-activity-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="accompany-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="accompany-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="board" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div id="board-result"></div>
            </div>
        </div>
    </div>
    @if ($logInfo)
        @include('parts.logsModal')
    @endif

@stop

@section('footer-scripts')
    <!-- Page JS -->
    <script type="text/javascript">
        function refreshLoadGame(game_id) {

            $("#loading").fadeIn();
            $.ajax({
                type: "POST",
                url: "{{ route('loadGame') }}",
                data: {
                    id: game_id,
                },
                success: function(data2) {
                    $("#load-game-result").html(data2);
                    if ($('.update-changes-pause-class').length < 1) {
                        $('.update-changes-class:last').attr('class',
                            'btn btn-success btn-sm update-changes')
                    }
                    $('.update-changes-class.btn-info').remove();
                    $("#loading").fadeOut();
                },
                error: function(data2) {
                    $("#loading").fadeOut();
                },
            });
        }

        function datetimeMask() {
            $('.datetime-mask-current').on('focus', function() {
                $(this).hide();
                $('.datetime-mask-custome').show();
                $('.datetime-mask-custome').trigger('click');
            });
            $('.datetime-mask-custome').persianDatepicker({
                dayPicker: {
                    enabled: false
                },
                monthPicker: {
                    enabled: false
                },
                yearPicker: {
                    enabled: false
                },
                calendarType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                initialValue: false,
                observer: true,
                format: 'HH:mm',
                altField: "#in-dateTime-value",
                altFormat: 'YYYY/MM/DD HH:mm:ss',
                timePicker: {
                    enabled: true,
                    meridiem: {
                        enabled: true
                    },
                    second: {
                        enabled: false,
                    }
                },
                initialValueType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                calendar: {
                    persian: {
                        locale: 'fa'
                    },
                    gregorian: {
                        locale: 'en',
                        format: 'HH:mm'
                    }
                },
                onHide: function() {
                    if (!($('#in-dateTime-value').val())) {
                        $('.datetime-mask-custome').hide();
                        $('.datetime-mask-current').show();
                    }
                },
                autoClose: true,
            });
        }

        function timePicker() {
            $('.timePicker').persianDatepicker({
                monthPicker: {
                    enabled: false
                },
                yearPicker: {
                    enabled: false
                },
                calendarType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                initialValue: false,
                observer: true,
                format: 'YYYY/MM/DD HH:mm',
                // altField: "#in-dateTime-value",
                // altFormat: 'YYYY/MM/DD HH:mm:ss',
                timePicker: {
                    enabled: true,
                    meridiem: {
                        enabled: true
                    },
                    second: {
                        enabled: false,
                    }
                },
                initialValueType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                calendar: {
                    persian: {
                        locale: 'fa',
                        format: 'YYYY/MM/DD HH:mm'
                    },
                    gregorian: {
                        locale: 'en',
                        format: 'YYYY/MM/DD HH:mm'
                    }
                },
                autoClose: true,
            });
        }

        $(document.body).on('click', '.edit-meta', function() {
            window.editMetaModal = new bootstrap.Modal($('#edit-meta-modal'), {});
            window.editMetaModal.show();
            // console.log($(this).html());

            viewAjax("{{ route('editGameMeta') }}", {
                id: $(this).data('id')
            }, '#edit-meta-result', function() {
                timePicker();
            })

        })

        $(document.body).on('click', '#store-meta-period-btn', function() {
            data = {
                target: 'edit-meta-period',
                id: $('#edit-meta-form input[name=meta_id]').val(),
                start: $('#edit-meta-form input[name=start]').val(),
                end: $('#edit-meta-form input[name=end]').val(),
            };
            actionAjax("{{ route('crudGameMeta') }}", data, null, null, function(response) {
                console.log(response);
                if (response.message) {
                    $('#edit-meta-error').html(response.message);
                    $('#edit-meta-error').show();
                    return 'done';
                } else {
                    window.editMetaModal.hide();
                    refreshLoadGame(response.g_id);
                }

            }, null, false);
        });

        // $('.edit-meta').on('click', function() {
        //     var modal2 = new bootstrap.Modal($('#modal2')[0], {
        //         backdrop: 'static', // Optional: keep modal2 open when clicking outside
        //     });

        //     modal2.show();
        // });

        if ("{{ $logInfo }}") {
            var myModal = new bootstrap.Modal(document.getElementById('log-modal'), {
                backdrop: 'static'
            });
            if (@json($logInfo?->check())) {
                myModal.show();
            }
        }
        let table_html = `
        <table id="game-table" class="table table-hover border-top">
            <thead>
                <tr>
                    @php echo $headers @endphp
                </tr>
            </thead>
        </table>`;

        let table;

        function getAttendees() {
            $('#attendees').html('(' + window.attendees + ')');
        }

        function makeTable(fields = null) {
            window.attendees = 0;
            $("#game-result").html(table_html);
            window['table'] = $("#game-table").DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: {
                    "url": "{{ route('indexGame') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: @php echo $columns @endphp,
                createdRow: function(row, data, index) {
                    // console.log(row);
                    window.attendees += data.count;
                    // console.log(index);
                    if (data['person_meta'] != '') {
                        $(row).addClass('bg-label-secondary');
                    }
                },
                initComplete: function(data) {

                    getAttendees();
                }
            });
            $('body').tooltip({
                selector: '[data-bs-toggle="tooltip"]'
            });

        }

        function makeTableBySection(section_id) {
            window.attendees = 0;
            $("#game-result").html(table_html);
            window['table'] = $("#game-table").DataTable({
                processing: true,
                serverSide: true,
                paging: false,
                ajax: {
                    "url": "{{ route('indexGame') }}",
                    "type": "GET",
                    "data": {
                        'section_id': section_id
                    }
                },
                columns: @php echo $columns @endphp,
                createdRow: function(row, data, index) {
                    window.attendees += data.count;
                    if (data['person_meta'] != '') {
                        $(row).addClass('bg-label-secondary');
                    }
                },
                initComplete: function() {
                    getAttendees();
                }
            });
            $('body').tooltip({
                selector: '[data-bs-toggle="tooltip"]'
            });
        }

        $(document).ready(function() {

            $.ajax({
                url: "{{ route('checkLicense') }}",
                type: "GET",
                success: function(response) {
                    if (!response.isActive) {
                        $('#crud-game').removeClass('inactive');
                    } else {
                        $('#crud-game').addClass('inactive');
                    }
                },
                error: function() {
                    console.error("مشکلی در بررسی وضعیت لایسنس به وجود آمد.");
                }
            });

            $(document.body).on("click", "#print-bill", function() {
                let id = $(this).data('id');
                let printTab = window.open('/print/' + id, '_blank');
            });
            makeTable();

            $(document.body).on("click", "#crud-game", function() {
                if ($(this).hasClass('inactive')) {
                    Swal.fire({
                        title: 'خطا',
                        text: 'در حالتی که آفلاین فعال باشد امکان ثبت ورود وجود ندارد.',
                        icon: 'warning',
                        confirmButtonText: 'باشه'
                    });
                } else {
                    $("#loading").fadeIn();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('crudGame') }}",
                        success: function(data) {
                            $("#loading").fadeOut();
                            $("#crud-result").html(data);
                            $(".select2-g").select2({
                                dropdownParent: $('#crud .modal-content')
                            });
                            $("#adjectives").select2({
                                dropdownParent: $('#crud .modal-content'),
                                placeholder: "@lang('جهت انتخاب امانتی ها کلیک کنید')..."
                            });
                            datetimeMask();
                            const dateMask = document.querySelector('.date-mask');
                            if (dateMask) {
                                $(".date-mask").persianDatepicker({
                                    initialValue: false,
                                    observer: true,
                                    format: 'YYYY/MM/DD',
                                });
                            }
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }

            });

            $(document.body).on("hidden.bs.modal", "#crud", function() {
                $("#crud-result").html('');
            });

            $(document.body).on("hidden.bs.modal", "#factor-modal", function() {
                $("#factor-result").html('');
            });

            $(document.body).on("hidden.bs.modal", "#game-modal", function() {
                $("#load-game-result").html('');
            });

            $(document.body).on("hidden.bs.modal", "#offer_modal1334", function() {
                $("#offer-result").html('');
            });

            $(document.body).on("submit", "#p-search-game", function(e) {
                $("#loading").fadeIn();
                e.preventDefault();
                var input = $("#search-person-input").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('personGame') }}",
                    data: {
                        input: input
                    },
                    success: function(data) {
                        $("#search-result").empty();
                        if (data.persons.length > 0) {
                            data.persons.forEach(function(person) {
                                const result = person["id"];
                                $("#search-result").append(
                                    `<a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between p-result"
                                        data-id="${person["id"]}" data-name="${person["name"]}" data-family="${person["family"]}"
                                        data-gender="${person["gender"]}" data-mobile="${person["mobile"]}" data-birth="${person["birth"]}"
                                        data-national_code="${person["national_code"]}" data-reg_code="${person["reg_code"]}"
                                        data-address="${person["address"]}">
                                        <div class="li-wrapper d-flex justify-content-start align-items-center">
                                            <div class="list-content">
                                                <h6 class="mb-1">${person["name"]} ${person["family"]}</h6>
                                                <small class="text-muted">${person["mobile"]}</small>
                                            </div>
                                        </div>
                                    </a>`
                                );
                            });
                        } else {
                            $("#search-result").append();
                        }
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".p-result", function() {
                var note = $(this).data("note")
                var id = $(this).data("id");
                var name = $(this).data("name");
                var family = $(this).data("family");
                var gender = $(this).data("gender");
                var mobile = $(this).data("mobile");
                @if (app()->getLocale() == 'fa')
                    if ($(this).data("birth") != null) {
                        var birth = new Date($(this).data("birth")).toLocaleDateString('fa-IR');
                    }
                @else
                    if ($(this).data("birth") != null) {
                        // var birth = new Date($(this).data("birth")).toLocaleDateString('fa-IR');
                        var birth = $(this).data("birth");
                    }
                @endif
                var national_code = $(this).data("national_code");
                var reg_code = $(this).data("reg_code");
                var address = $(this).data("address");

                $("#person-note").val(note);
                $("#person-id").val(id);
                $("#person-name").val(name);
                $("#person-family").val(family);
                $("#person-gender").val(gender);
                $("#person-mobile").val(mobile);
                $("#person-birth").val(birth);
                $("#person-nationalcode").val(national_code);
                $("#person-regcode").val(reg_code);
                $("#person-address").val(address);
                viewAjax("{{ route('getPersonNote') }}", {
                    id: id
                }, "#pesrson-note");
                $("#search-result").html("");
            });

            $(document.body).on("click", "#store-game", function(e) {
                e.preventDefault();
                var e = checkEmpty("game-form");
                var m = checkMobile("game-form");
                var n = checkNationalCode("game-form");
                if (!e && !n && !m) {
                    Swal.fire({
                        title: "{{ __('لطفا کمی صبر نمایید...') }}",
                        text: "{{ __('در حال پردازش هستیم.') }}",
                        icon: "info",
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    var person_id = $("#person-id").val();
                    var inDateTime = $("#in-dateTime-value").val();

                    var person_name = $("#person-name").val();
                    var person_family = $("#person-family").val();
                    var person_gender = $("#person-gender").val();
                    var person_mobile = $("#person-mobile").val();
                    var person_birth = $("#person-birth").val();
                    var person_national_code = $("#person-nationalcode").val();
                    var person_reg_code = $("#person-regcode").val();
                    var person_address = $("#person-address").val();
                    var section_id = $("#section-id").val();
                    var station_id = $("#station-id").val();
                    var counter_id = $("#counter-id").val();
                    var count = $("#count").val();
                    var seperate = $("#seperate").is(":checked");
                    var deposit = $("#deposit").val();
                    var deposit_type = $("#deposit-type").val();
                    var rate = $("#rate").val();
                    var adjectives = $("#adjectives").val();
                    var accompany_name = $("#accompany-name").val();
                    var accompany_mobile = $("#accompany-mobile").val();
                    var accompany_relation = $("#accompany-relation").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeGame') }}",
                        data: {
                            person_id: person_id,
                            person_name: person_name,
                            person_family: person_family,
                            person_gender: person_gender,
                            person_mobile: person_mobile,
                            person_birth: person_birth,
                            person_national_code: person_national_code,
                            person_reg_code: person_reg_code,
                            person_address: person_address,
                            section_id: section_id,
                            station_id: station_id,
                            counter_id: counter_id,
                            inDateTime: inDateTime,
                            count: count,
                            deposit: deposit,
                            deposit_type: deposit_type,
                            rate: rate,
                            seperate: seperate,
                            adjectives: adjectives,
                            accompany_name: accompany_name,
                            accompany_mobile: accompany_mobile,
                            accompany_relation: accompany_relation
                        },
                        success: function(data) {
                            makeTable();
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('با موفقیت ثبت شد.') }}",
                                icon: "success"
                            });

                            $("#person-id").val(0);
                            $("#person-name").val('');
                            $("#person-family").val('');
                            $("#person-gender").val(0);
                            $("#person-mobile").val('');
                            $("#person-birth").val('');
                            $("#person-nationalcode").val('');
                            $("#person-regcode").val('');
                            $("#person-address").val('');
                            $("#counter-id").val('');
                            $("#count").val(1);
                            $("#deposit").val('');
                            $("#deposit-type").val('');
                            $("#rate").val('normal');
                            $("#adjectives").val('');
                            $("#accompany-name").val('');
                            $("#accompany-mobile").val('');
                            $("#accompany-relation").val('');
                            $("#in-dateTime-value").val(null);
                            $("#in-dateTime").val(null);
                            $('.datetime-mask-current').show();
                            $('.datetime-mask-custome').hide();

                        },
                        error: function(data) {
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('لطفا کادرهای ستاره دار را تکمیل نمایید.') }}",
                        icon: "error"
                    });
                }
            });

            $(document.body).on("click", ".close-game", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('لطفا کمی صبر نمایید...') }}",
                    text: "{{ __('در حال پردازش هستیم.') }}",
                    icon: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                var g_id = $(this).data("g_id");
                var payments = $(".payment-price").serialize();
                var details = $(".payment-details").serialize();
                var no_logout_message = $("#no-logout-message").is(":checked");
                var no_feedback_message = $("#no-feedback-message").is(":checked");
                var no_club_message = $("#no-club-message").is(":checked");
                $.ajax({
                    type: "POST",
                    url: "{{ route('closeGame') }}",
                    data: {
                        g_id: g_id,
                        payments: payments,
                        details: details,
                        no_logout_message: no_logout_message,
                        no_feedback_message: no_feedback_message,
                        no_club_message: no_club_message
                    },
                    success: function(data) {
                        makeTable()
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                            icon: "success"
                        }).then(() => {
                            $("#close-game-modal").click();
                            $('#load-game-result').html('');
                        });
                    },
                    error: function(data) {
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".crud-offer", function() {
                var g_id = $(this).data("g_id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudGameOffer') }}",
                    data: {
                        g_id: g_id,
                    },
                    success: function(data) {
                        $("#offer-result").html(data);
                        if ($("#offer-type").val() == 1) {
                            $(".offer-code-field").hide();
                            $("#offer-code").val('');
                            $(".offer-price-field").show();
                        } else if ($("#offer-type").val() == 2) {
                            $(".offer-price-field").hide();
                            $("#offer-price").val('');
                            $("#offer-calc").val('');
                            $(".offer-code-field").show();
                        }
                    },
                    error: function(data) {
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("change", "#offer-type", function() {
                if ($(this).val() == 1) {
                    $(".offer-code-field").hide();
                    $("#offer-code").val('');
                    $(".offer-price-field").show();
                } else if ($(this).val() == 2) {
                    $(".offer-price-field").hide();
                    $("#offer-price").val('');
                    $("#offer-calc").val('');
                    $(".offer-code-field").show();
                }
            });

            $(document.body).on("click", "#store-offer", function() {
                var e = checkEmpty();
                if (!e) {
                    var g_id = $(this).data("g_id");
                    var offer_type = $("#offer-type").val();
                    var offer_code = $("#offer-code").val();
                    var offer_price = $("#offer-price").val();
                    var offer_calc = $("#offer-calc").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeGameOffer') }}",
                        data: {
                            g_id: g_id,
                            offer_type: offer_type,
                            offer_code: offer_code,
                            offer_price: offer_price,
                            offer_calc: offer_calc,
                        },
                        success: function(data) {
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('تخفیف اعمال شد.') }}",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", ".crud-person-info", function() {
                var p_id = $(this).data("p_id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudGamePersonInfo') }}",
                    data: {
                        p_id: p_id,
                    },
                    success: function(data) {
                        $("#person-info-result").html(data);
                    },
                    error: function(data) {
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-person-info", function() {
                var id = $(this).data("p_id");
                var data = $("#person-info-form").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ route('storePersonMeta') }}",
                    data: {
                        id: id,
                        data: data,
                    },
                    success: function(data) {
                        makeTable();
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                            icon: "success"
                        });
                    },
                    error: function(data) {
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".crud-factor", function() {
                var g_id = $(this).data("g_id");
                var f_type = $(this).data("f_type");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudFactor') }}",
                    data: {
                        g_id: g_id,
                        f_type: f_type,
                    },
                    success: function(data) {
                        $("#factor-result").html(data);
                        if ($("#offer-type").val() == 1) {
                            $(".offer-code-field").hide();
                            $("#offer-code").val('');
                            $(".offer-price-field").show();
                        } else if ($("#offer-type").val() == 2) {
                            $(".offer-price-field").hide();
                            $("#offer-price").val('');
                            $("#offer-calc").val('');
                            $(".offer-code-field").show();
                        }
                        $(".select2-f").select2({
                            dropdownParent: $('#factor-modal .modal-content')
                        });
                    },
                    error: function(data) {
                        console.log(data);
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            let f_p_search;

            $(document.body).on("click", "#f-product-search", function() {
                $("#loading").fadeIn();
                var f_type = $(this).data("f_type");
                var f_id = $(this).data("f_id");
                var g_id = $(this).data("g_id");
                var name = $("#f-product-name").val();
                var category = $("#f-product-category").val();
                var search_data = {
                    name: name,
                    category: category
                }

                $.ajax({
                    url: "{{ route('productFactor') }}",
                    type: "POST",
                    data: {
                        f_type: f_type,
                        f_id: f_id,
                        g_id: g_id,
                        name: name,
                        category: category
                    },
                    success: function(data) {
                        $("#factor-products").html(data);
                        window['f_product_search'] = search_data;
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            })
            window.confirmTitle = "{{ __('اطمینان دارید؟') }}";
            window.confirmButtonText = "{{ __('بله، مطمئنم.') }}";
            window.cancelButtonText = "{{ __('نه، پشیمون شدم.') }}";
            $(document.body).on("click", "#delete-game", function() {
                confirmAction('درحال حذف کامل ورود', () => {
                    actionAjax('{{ route('deleteGame') }}', {
                        id: $(this).data("id"),
                    }, "موفق", 'ورود با موفقیت حذف شد', () => {
                        $('#close-game-modal').trigger('click');
                        makeTable();
                    });
                })
            });

            $(document.body).on("click", ".store-body", function() {
                $("#loading").fadeIn();
                var f_id = $(this).data("f_id");
                var f_type = $(this).data("f_type");
                var g_id = $(this).data("g_id");
                var p_id = $(this).data("p_id");
                var person_id = $("#person-id").val();
                var person_name = $("#person-name").val();
                var person_family = $("#person-family").val();
                var person_gender = $("#person-gender").val();
                var person_mobile = $("#person-mobile").val();
                var data = {
                    f_id: f_id,
                    f_type: f_type,
                    g_id: g_id,
                    p_id: p_id,
                    person_id: person_id,
                    person_name: person_name,
                    person_family: person_family,
                    person_gender: person_gender,
                    mobile: person_mobile,
                }
                $.extend(data, window["f_product_search"]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('storeFactor') }}",
                    data: data,
                    success: function(data) {
                        $("#loading").fadeOut();
                        $("#factor-result").html(data);
                        if ($("#offer-type").val() == 1) {
                            $(".offer-code-field").hide();
                            $("#offer-code").val('');
                            $(".offer-price-field").show();
                        } else if ($("#offer-type").val() == 2) {
                            $(".offer-price-field").hide();
                            $("#offer-price").val('');
                            $("#offer-calc").val('');
                            $(".offer-code-field").show();
                        }
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        console.log(data);
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });


            $(document.body).on("change", ".update-factor", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                var f_type = $(this).data("f_type");
                var game_id = $(this).data("game");
                var product_price = $(`input[name=p-price][data-id=${id}]`).val();
                var count = $(`input[name=count][data-id=${id}]`).val();
                var data = {
                    id: id,
                    f_type: f_type,
                    product_price: product_price,
                    count: count,
                }

                $.extend(data, window["f_product_search"]);

                $.ajax({
                    type: "POST",
                    url: "{{ route('updateFactor') }}",
                    data: data,
                    success: function(data) {

                        if ($("#offer-type").val() == 1) {
                            $(".offer-code-field").hide();
                            $("#offer-code").val('');
                            $(".offer-price-field").show();
                        } else if ($("#offer-type").val() == 2) {
                            $(".offer-price-field").hide();
                            $("#offer-price").val('');
                            $("#offer-calc").val('');
                            $(".offer-code-field").show();
                        }
                        if (f_type == "load-game") {
                            // $("#g-factor-result").html(data);
                            refreshLoadGame(game_id);
                        } else if (f_type == "game") {
                            $("#foctor-bodies").html(data);
                            $("#loading").fadeOut();
                        } else {
                            $("#factor-result").html(data);
                            $("#loading").fadeOut();

                        }
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        console.log(data);
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".delete-body", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از حذف این مورد اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        var f_type = $(this).data("f_type");
                        var game_id = $(this).data("game");
                        var data = {
                            id: id,
                            f_type: f_type,
                        }

                        $.extend(data, window["f_product_search"]);

                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteFactor') }}",
                            data: data,
                            success: function(data) {
                                $("#loading").fadeOut();
                                if (f_type == 'load-game') {
                                    // $("#g-factor-result").html(data);
                                    refreshLoadGame(game_id);
                                } else {
                                    $("#factor-result").html(data);
                                }
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                console.log(data);
                                Swal.fire({
                                    title: "{{ __('اخطار') }}",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            $(document.body).on("click", ".close-factor", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از بستن فاکتور اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var factor_id = $(this).data("f_id");
                        var person_id = $("#person-id").val();
                        var payments = $(".payment-price").serialize();
                        var details = $(".payment-details").serialize();

                        $.ajax({
                            type: "POST",
                            url: "{{ route('closeFactor') }}",
                            data: {
                                factor_id: factor_id,
                                person_id: person_id,
                                payments: payments,
                                details: details
                            },
                            success: function(data) {
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('عملیات موفق') }}",
                                    text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                                    icon: "success"
                                });
                                $("#close-factor-modal").click();
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                console.log(data);
                                Swal.fire({
                                    title: "{{ __('اخطار') }}",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            $(document.body).on("change", ".factor-offer-field", function() {
                $("#loading").fadeIn();
                var factor_id = $(this).data("f_id");
                var offer_type = $("#offer-type").val();
                var offer_code = $("#offer-code").val();
                var offer_price = $("#offer-price").val();
                var data = {
                    factor_id: factor_id,
                    offer_type: offer_type,
                    offer_code: offer_code,
                    offer_price: offer_price
                }
                $.extend(data, window["f_product_search"]);
                $.ajax({
                    type: "POST",
                    url: "{{ route('offerFactor') }}",
                    data: data,
                    success: function(data) {
                        $("#loading").fadeOut();
                        $("#factor-result").html(data);
                        if ($("#offer-type").val() == 1) {
                            $(".offer-code-field").hide();
                            $("#offer-code").val('');
                            $(".offer-price-field").show();
                        } else if ($("#offer-type").val() == 2) {
                            $(".offer-price-field").hide();
                            $("#offer-price").val('');
                            $("#offer-calc").val('');
                            $(".offer-code-field").show();
                        }
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        console.log(data);
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".crud-changes", function() {
                var g_id = $(this).data("g_id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudGameChanges') }}",
                    data: {
                        g_id: g_id,
                    },
                    success: function(data) {
                        $("#changes-result").html(data);
                    },
                    error: function(data) {
                        console.log(data);
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".price_type_error", function() {
                Swal.fire({
                    title: "{{ __('توجه') }}",
                    text: "{{ __('هنگام استفاده از تعرفه کلی امکان توقف وجود ندارد') }}",
                    icon: "warning"
                });
            });

            $(document.body).on("click", "#store-changes", function() {
                var g_id = $(this).data("g_id");
                var count = $("#meta-count").val();
                var type = $("#meta-type").val();
                if (count == "") {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('کادر تعداد نمی تواند خالی باشد.') }}",
                        icon: "error"
                    });
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('storeGameChanges') }}",
                    data: {
                        g_id: g_id,
                        count: count,
                        type: type,
                    },
                    success: function(data) {

                        $("#meta-result").html(data);
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                            icon: "success"
                        });
                        makeTable();
                    },
                    error: function(data) {
                        console.log(data);
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".update-changes", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از تغییر این مورد اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        var action = $(this).data("action");
                        var type = $(this).data("type");
                        // if (action=='play') {
                        //     $(this).attr('class','btn btn-secondery btn-sm')
                        // }
                        $.ajax({
                            type: "POST",
                            url: "{{ route('updateGameChanges') }}",
                            data: {
                                id: id,
                                action: action,
                                type: type,
                            },
                            success: function(data) {
                                $("#meta-result").html(data);
                                if ($('.update-changes-pause-class').length < 1) {
                                    $('.update-changes-class:last').attr('class',
                                        'btn btn-success btn-sm update-changes')
                                }
                                $('.update-changes-class.btn-info').remove();
                                Swal.fire({
                                    title: "{{ __('عملیات موفق') }}",
                                    text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                                    icon: "success"
                                });
                                $("#loading").fadeOut();
                            },
                            error: function(data) {
                                console.log(data);
                                Swal.fire({
                                    title: "{{ __('اخطار') }}",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                                $("#loading").fadeOut();
                            }
                        });
                    }
                });
            });
            $(document.body).on("click", ".update-deposit", function() {
                if ($('#deposit-type').val() == '') {
                    Swal.fire({
                        title: "@lang('خطا')",
                        text: 'لطفا نوع پیش پرداخت را انتخاب کنید',
                        icon: "error",
                    });
                    return 'out';
                }
                actionAjax("{{ route('storeGameChanges') }}", {
                        type: 'update-deposit',
                        value: $('#deposit').val(),
                        paymentType: $('#deposit-type').val(),
                        g_id: $('#deposit').attr('data-g_id')
                    }, '{{ __('موفق') }}', '{{ __('مبلغ پیش پرداخت با موفقیت ویرایش شد') }}',
                    function(g_id) {
                        refreshLoadGame(g_id);
                    });
            });
            $(document.body).on("click", ".delete-changes", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از حذف این مورد اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        var id = $(this).data("id");
                        var g_id = $(this).data("g_id");
                        $("#loading").fadeIn();
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteGameChanges') }}",
                            data: {
                                id: id,
                            },
                            success: function(data) {
                                makeTable();
                                refreshLoadGame(g_id);
                                $("#meta-result").html(data);
                                if ($('.update-changes-pause-class').length < 1) {
                                    $('.update-changes-class:last').attr('class',
                                        'btn btn-success btn-sm update-changes')
                                }
                                $('.update-changes-class.btn-info').remove();
                                $("#loading").fadeOut();

                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                console.log(data);
                                Swal.fire({
                                    title: "{{ __('اخطار') }}",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            $(document.body).on("click", ".print-accompany", function() {
                $('#accompany-info').print();
            });

            $(document.body).on("click", "#crud-user-activity", function() {
                $("#loading").fadeIn();
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudUserActivity') }}",
                    success: function(data) {
                        $("#crud-user-activity-result").html(data);
                        $(".select2-u").select2({
                            dropdownParent: $('#user-activity-modal .modal-content')
                        });
                        $("#loading").fadeOut(0);
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-activity-in", function() {
                var e = checkEmpty('user-activity-form')
                if (!e) {
                    $("#loading").fadeIn();
                    var user_id = $("#user-id").find("option:selected").val();
                    var action = "in";
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeUserActivity') }}",
                        data: {
                            user_id: user_id,
                            action: action
                        },
                        success: function(data) {
                            $("#activity-result").html(data);
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", ".store-activity-out", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از ثبت خروج اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        var action = "out";

                        $.ajax({
                            type: "POST",
                            url: "{{ route('storeUserActivity') }}",
                            data: {
                                id: id,
                                action: action
                            },
                            success: function(data) {
                                $("#activity-result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('عملیات موفق') }}",
                                    text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                                    icon: "success"
                                });
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('اخطار') }}",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            $(document.body).on("click", ".accompany-modal", function() {
                $("#loading").fadeIn();
                var g_id = $(this).data("g_id");

                $.ajax({
                    type: "POST",
                    url: "{{ route('accompanyGame') }}",
                    data: {
                        g_id: g_id
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        $('#accompany-result').html(data);
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("change", ".payment-price", function() {
                totalPayment();
            });

            $(document.body).on("click", ".edit-counter", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('editCounter') }}",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        $("#board-result").html(data);
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".update-counter", function() {
                var e = checkEmpty();
                if (!e) {
                    var id = $(this).data("id");
                    var action_value = $("#action-value").val();
                    var type = $(this).data('type');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('updateCounter') }}",
                        data: {
                            id: id,
                            action_value: action_value,
                            type: type
                        },
                        success: function(data) {
                            if (type == "create") {
                                makeTable();
                            }
                            Swal.fire({
                                type: "POST",
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('با موفقیت تغییر یافت.') }}",
                                icon: "success"
                            });
                            $('.btn-close').trigger('click');

                        },
                        error: function(data) {
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", "#toggle-checkbox", function() {
                $(".select-game").prop("checked", false);
                $.ajax({
                    url: "{{ route('getGroupsGame') }}",
                    type: "GET",
                    success: function(data) {
                        $("#group-checkbox").html(data);
                        let column = window['table'].column(0);
                        column.visible(!column.visible());
                    },
                    error: function(data) {
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("submit", "#g-search-game", function(e) {
                $("#loading").fadeIn();
                e.preventDefault();
                var name = $("#search-group-input").val();
                if (!name == "") {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('searchGroup') }}",
                        data: {
                            name: name
                        },
                        success: function(data) {
                            $("#search-group-result").empty();
                            if (data.length > 0) {
                                data.forEach(function(group) {
                                    $("#search-group-result").append(
                                        `<a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between g-result" data-id="${group["id"]}">
                                            <div class="li-wrapper d-flex justify-content-start align-items-center">
                                                <div class="list-content">
                                                    <h6 class="mb-1">${ group["name"] }</h6>
                                                    <small class="text-muted">${ group["details"] ?? '' }</small>
                                                </div>
                                            </div>
                                        </a>`
                                    );
                                });
                            }
                            $("#loading").fadeOut();
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", ".g-result", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    url: "{{ route('showPeopleGroup') }}",
                    type: "GET",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $("#search-group-result").html("");
                        $("#g-group-id").val(id);
                        $("#group-people").html(data);
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("change", "#check-all", function() {
                if ($(this).is(":checked")) {
                    $(".g-person-check").prop('checked', true);
                } else {
                    $(".g-person-check").prop('checked', false);
                }
            });

            $(document.body).on("click", "#store-group-game", function() {
                $("#loading").fadeIn();
                var people = $(".g-person-check").serialize();
                if (people == "") {
                    $("#loading").fadeOut();
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('حداقل یک شخص باید انتخاب شود.') }}",
                        icon: "error"
                    });
                    return;
                }
                var e = checkEmpty('g-game-form');
                if (!e) {
                    var section_id = $("#g-section-id").val();
                    var group_id = $("#g-group-id").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('groupStoreGame') }}",
                        data: {
                            section_id: section_id,
                            group_id: group_id,
                            people: people
                        },
                        success: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                                icon: "success"
                            });
                            console.log(data);
                            makeTable();
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("change", "input[name=radio]", function() {
                $("#loading").fadeIn();
                value = $(this).val();
                if (value == "person") {
                    $("#single-entrance").show();
                    $("#group-entrance").hide();
                } else if (value == "group") {
                    $("#single-entrance").hide();
                    $("#group-entrance").show();
                }
                $("#loading").fadeOut();
            });

            $(document.body).on("click", ".group-check", function() {
                if ($(this).is(":checked")) {
                    $(`.select-game[data-group-id=${$(this).val()}]`).prop('checked', true);
                } else {
                    $(`.select-game[data-group-id=${$(this).val()}]`).prop('checked', false);
                }
            });

            $(document.body).on("click", "#group-check-all", function() {
                if ($(this).is(":checked")) {
                    $(`.select-game`).prop('checked', true);
                } else {
                    $(`.select-game`).prop('checked', false);
                }
            });

            $(document.body).on("click", "#group-close", function() {
                var games = $(".select-game").serialize();
                if (games == '') {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('حداقل یک ردیف باید انتخاب شود.') }}",
                        icon: "error"
                    });
                    return;
                }
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('groupPriceGame') }}",
                    type: "GET",
                    data: {
                        games: games
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        groupClose(games, data);
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("change", "#section-filter", function() {
                var section_id = $(this).val();
                makeTableBySection(section_id);
            });

            function groupClose(games, totalPrice) {
                Swal.fire({
                    title: "{{ __('مجموع: ') }}" + addCommas(totalPrice),
                    text: "{{ __('آیا سند پرداخت برای این افراد ثبت شود؟') }}",
                    icon: "question",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله') }}",
                    denyButtonText: "{{ __('خیر') }}",
                    cancelButtonText: "{{ __('انصراف') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        data = {
                            games: games,
                            payment: true
                        }
                        groupCloseAjax(data);
                    } else if (result.isDenied) {
                        data = {
                            games: games,
                            payment: false
                        }
                        groupCloseAjax(data);
                    }
                });
            }

            function groupCloseAjax(data) {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('groupCloseGame') }}",
                    type: "POST",
                    data: data,
                    success: function(data) {
                        $("#toggle-checkbox").click();
                        window['table'].ajax.reload(function(data) {
                            window.attendees = 0;

                            $(data.data).map(function(key, item) {
                                window.attendees += item.count;
                            });
                            // window.attendees-=data;
                            getAttendees();
                        });
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            icon: "success",
                            text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}"
                        });
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            }

        });
    </script>
    <script type="text/javascript" src="{{ asset('assets/script/jQuery.print.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/script/game.js') }}"></script>
@stop
