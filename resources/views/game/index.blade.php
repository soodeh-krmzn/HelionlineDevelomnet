@extends('parts.master')

@section('title', __('گزارش بخش'))

@section('head-styles')
    <style>
        @media print {
            #bill-print-area table * {
                font-size: 3vw !important;
            }

            /* #bill-print-area {
                        width: 100% !important;
                    } */

            #bill-print-area table {
                min-width: initial !important;
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
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="collapse" id="collapseExample">
                <div class="card-header border-bottom">
                    <div class="py-3 primary-font">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('شخص') }}</label>
                                    <select name="s-person-id" id="s-person-id" class="searchPerson">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('بخش') }}</label>
                                    <select name="s-section-id" id="s-section-id" class="form-select select2-2">
                                        <option value="">{{ __('انتخاب') }}</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از تاریخ') }}</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask"
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا تاریخ') }}</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask"
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="s-trashed">
                                        <label class="form-check-label"
                                            for="s-trashed">{{ __('نمایش موارد حذف شده') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-9 text-end">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-payment">{{ __('جستجو') }}</button>
                                    <button class="btn btn-info" id="show-all">{{ __('نمایش همه') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> <span
                                class="d-none d-lg-inline-block">{{ __('جستجو') }}</span>
                        </button>
                    </div>
                    <div class="col">
                        <div
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                    <a href="/game-report" class="btn btn-info ms-2 rounded">
                                        <span>
                                            <i class="bx bx-history"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('اصلاحات') }}</span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col">

                    </div>
                </div>
                <div class="row mx-1">
                    <div class="col-md-6">
                        <div id="sum-container" class="alert alert-warning my-3 text-center">
                            {{ __('جمع کل') }}:
                            <span id="sum"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="sum-container" class="alert alert-warning my-3 text-center">
                            {{ __(' مجموع زمان') }}:
                            <span id="sum-hours"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="crud" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="crud-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="meta-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <h3 class="text-center">{{ __('جزئیات کد') }} <span id="game-id"></span></h3>
                    <div id="meta-result"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- load-game-modeal --}}
    <div class="modal fade" id="game-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3">
                <div id="load-game-result"></div>
            </div>
        </div>
    </div>
    {{-- load-game-modal END --}}
    <div class="modal fade" id="accompany-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="accompany-result"></div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script type="text/javascript" src="{{ asset('assets/script/jQuery.print.js') }}"></script>
    <script type="text/javascript">
        searchPerson();
        let table = `<div class="table-responsive">
        <table id="game-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>{{ __('ردیف') }}</th>
                    <th>{{ __('کد') }}</th>
                    <th>{{ __('شخص') }}</th>
                    <th>{{ __('بخش') }}</th>
                    <th>{{ __('ورود') }}</th>
                    <th>{{ __('خروج') }}</th>
                    <th>{{ __('فروشگاه') }}</th>
                    <th>{{ __('بازی') }}</th>
                    <th>{{ __('تخفیف') }}</th>
                    <th>{{ __('شارژ') }}</th>
                    <th>{{ __('جمع') }}</th>
                    <th>{{ __('مدیریت') }}</th>
                </tr>
            </thead>
        </table></div>`;

        function sumGame(fields) {
            $.ajax({
                url: "{{ route('sumGame') }}",
                type: "GET",
                data: fields ?? {},
                success: function(data) {
                    $("#sum").html(addCommas(data.sumPrice));
                    $("#sum-hours").html(addCommas(data.sumHours));
                }
            });
        }

        $(document.body).on("click", "#print-bill", function() {
            let id = $(this).data('id');
            let printTab = window.open('/print/' + id, '_blank');
        });
        $(document.body).on('click', '.game-details', function() {
            let id = $(this).data('g_id');
            // alert(id);
            LoadGameDetails(id);
        })

        function LoadGameDetails(game_id) {
            $("#loading").fadeIn();
            $.ajax({
                type: "POST",
                url: "{{ route('loadGame', ['details' => true]) }}",
                data: {
                    id: game_id,
                },
                success: function(data2) {
                    $("#load-game-result").html(data2);
                    $("#loading").fadeOut();
                },
                error: function(data2) {
                    $("#loading").fadeOut();
                },
            });
        }
        $(document.body).on("click", ".print-bill", function() {
            $('#bill-print-area').print();
        });

        function makeTable(fields = null) {
            $("#result").html(table);
            var data_table = $("#game-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableGame') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order: [5, 'desc'],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'section_name'
                    },
                    {
                        data: 'in_time'
                    },
                    {
                        data: 'out_time'
                    },
                    {
                        data: 'total_shop'
                    },
                    {
                        data: 'game_price'
                    },
                    {
                        data: 'offer_price'
                    },
                    {
                        data: 'used_sharj'
                    },
                    {
                        data: 'final_price'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            sumGame(fields);
            return data_table;
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            window.data_table = makeTable();

            let data;

            $(document.body).on("click", "#search-payment", function() {
                $("#loading").fadeIn();
                var person_id = $("#s-person-id").val();
                var section_id = $("#s-section-id").val();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var trashed = $("#s-trashed").is(":checked");
                var data = {
                    'person_id': person_id,
                    'section_id': section_id,
                    'from_date': from_date,
                    'to_date': to_date,
                    'trashed': trashed,
                    'filter_search': true
                }
                window['data'] = data;
                window.data_table = makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", ".crud-meta", function() {
                $("#loading").fadeIn();
                var g_id = $(this).data("g_id");
                $("#game-id").html(g_id);
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudGameMeta') }}",
                    data: {
                        g_id: g_id,
                    },
                    success: function(data) {
                        $("#meta-result").html(data);
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

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('editGame') }}",
                    data: {
                        id: id,
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
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
            $(".select2-2").select2({
                placeholder: "@lang('انتخاب')"
            });
            $(document.body).on("click", "#update-game", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var id = $(this).data("id");
                    var i = $("#in").val();
                    var out = $("#out").val();
                    var offer_price = $("#offer-price").val();
                    var game_price = $("#game-price").val();
                    var factor_price = $("#factor-price").val();
                    var section_id = $("#section-id").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('updateGame') }}",
                        data: {
                            id: id,
                            in: i,
                            out: out,
                            offer_price: offer_price,
                            game_price: game_price,
                            factor_price: factor_price,
                            section_id: section_id
                        },
                        success: function(data) {
                            // makeTable();
                            window.data_table.ajax.reload(function() {
                                sumGame(window['data'])
                            }, false);
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('با موفقیت ثبت شد.') }}",
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
                            makeTable();
                        }
                    });
                }
            });

            $(document.body).on("click", "#show-all", function() {
                var data = {
                    'all': true
                }
                window['data'] = data;
                window.data_table = makeTable(data);
            });

            $(document.body).on("click", ".delete-game", function() {
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
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteGame') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                window.data_table.ajax.reload(function() {
                                    sumGame(window['data']);
                                }, false);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('حذف شد') }}",
                                    text: "{{ __('مورد با موفقیت حذف شد.') }}",
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
                                makeTable();
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
                        $('#accompany-result').html(data);
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

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportGame') }}",
                    type: "POST",
                    data: window['data'],
                    success: function() {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            icon: "success",
                            text: "{{ __('گزارش گیری انجام شد. از صفحه گزارشات اکسل میتوانید این گزارش را دانلود کنید.') }}"
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
            });

        });
    </script>
@stop
