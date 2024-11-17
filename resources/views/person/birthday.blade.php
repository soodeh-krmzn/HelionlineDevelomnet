@extends('parts.master')

@section('title', 'اشخاص')

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="collapse" id="collapseExample">
                <div class="card-header border-bottom">
                    <div class="py-3 primary-font">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label"><i class='bx bx-search me-1'></i></label>
                                    <select id="type" class="form-select">
                                        <option value="d_m">روز و ماه</option>
                                        <option value="f_t">از تاریخ تا تاریخ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d_m">
                                <div class="form-group">
                                    <label class="form-label">روز</label>
                                    <select id="day" class="form-select">
                                        <option value="">@lang('همه')</option>
                                        <option value="01">1</option>
                                        <option value="02">2</option>
                                        <option value="03">3</option>
                                        <option value="04">4</option>
                                        <option value="05">5</option>
                                        <option value="06">6</option>
                                        <option value="07">7</option>
                                        <option value="08">8</option>
                                        <option value="09">9</option>
                                        <option>10</option>
                                        <option>11</option>
                                        <option>12</option>
                                        <option>13</option>
                                        <option>14</option>
                                        <option>15</option>
                                        <option>16</option>
                                        <option>17</option>
                                        <option>18</option>
                                        <option>19</option>
                                        <option>20</option>
                                        <option>21</option>
                                        <option>22</option>
                                        <option>23</option>
                                        <option>24</option>
                                        <option>25</option>
                                        <option>26</option>
                                        <option>27</option>
                                        <option>28</option>
                                        <option>29</option>
                                        <option>30</option>
                                        <option>31</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 d_m">
                                <div class="form-group">
                                    <label class="form-label">ماه</label>
                                    <select name="" id="month" class="form-select">
                                        <option value="">@lang('همه')</option>
                                        <option value="01">فروردین</option>
                                        <option value="02">اردیبهشت</option>
                                        <option value="03">خرداد</option>
                                        <option value="04">تیر</option>
                                        <option value="05">مرداد</option>
                                        <option value="06">شهریور</option>
                                        <option value="07">مهر</option>
                                        <option value="08">آبان</option>
                                        <option value="09">آذر</option>
                                        <option value="10">دی</option>
                                        <option value="11">بهمن</option>
                                        <option value="12">اسفند</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 f_t" style="display:none">
                                <div class="form-group">
                                    <label class="form-label">@lang('از تاریخ')</label>
                                    <input type="text" id="from-birthday" class="form-control date-mask "
                                        placeholder="1401/01/01">
                                </div>
                            </div>
                            <div class="col-md-4 f_t" style="display:none">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا تاریخ')</label>
                                    <input type="text" id="to-birthday" class="form-control date-mask "
                                        placeholder="1401/01/01">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-end justify-content-end">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-person">@lang('جستجو')</button>
                                    <a href="{{ route('birthdayPerson') }}" class="btn btn-info" id="show-today">نمایش
                                        امروز</a>
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
                                class="d-none d-lg-inline-block">@lang('جستجو')</span>
                        </button>
                        <button id="forward-birth" class="btn btn-info btn-sm me-1" type="button">
                            <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">
                                تولد های
                            </span>
                            <input type="number" class="form-control mx-1" style="width: 60px;height: 30px;"
                                id="days-forward" value="0">
                            روز بعد
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
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                </div>
                                {{-- <button type="button" class="btn add-new btn-info ms-2 mb-3 mb-sm-0 crud"
                                    data-bs-toggle="modal" data-bs-target="#crud">
                                    <span>ارسال خودکار</span>
                                </button> --}}
                                <button class="btn btn-secondary add-new btn-primary send-sms ms-2" data-action="all"
                                    data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                                    <span>
                                        <i class="bx bx-envelope me-0 me-lg-2"></i>
                                        <span class="d-none d-lg-inline-block">ارسال به همه</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
                </div>
            </div>

            <div class="modal fade" id="crud" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-3 p-md-5">
                        <div id="crud-result"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('footer-scripts')
    <script type="text/javascript">
        window.daysForward = 0;
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = `<div class="table-responsive">
            <table id="testTable" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>@lang('ردیف')</th>
                        <th>@lang('نام')</th>
                        <th>@lang('نام خانوادگی')</th>
                        <th>تاریخ تولد</th>
                        <th>تاریخ آخرین ارسال</th>
                        <th>@lang('موبایل')</th>
                        <th>@lang('عملیات')</th>
                    </tr>
                </thead>
            </table></div>`;

            function makeTable(fields = null) {
                $("#result").html(table);
                $("#testTable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{ route('tablePerson') }}",
                        "type": "GET",
                        "data": fields ?? {}
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'family'
                        },
                        {
                            data: 'birth'
                        },
                        {
                            data: 'lastSend'
                        },
                        {
                            data: 'mobile'
                        },
                        {
                            data: 'action2',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            window["data"] = {
                "day": "{{ getDay() }}",
                "month": "{{ getMonth() }}",
                "filter_search": true
            }

            makeTable(window["data"]);

            $(document.body).on("change", "#type", function() {
                if ($(this).val() == "d_m") {
                    $(".f_t").hide();
                    $("#from-birthday").val('');
                    $("#to-birthday").val('');
                    $(".d_m").show();
                } else if ($(this).val() == "f_t") {
                    $(".d_m").hide();
                    $("#day").val('');
                    $("#month").val('');
                    $(".f_t").show();
                }
            });

            $(document.body).on("click", "#search-person", function() {
                var d = checkDate();
                if (d) {
                    Swal.fire({
                        title: "@lang('اخطار')",
                        icon: 'error',
                        text: 'تاریخ وارد شده معتبر نمیباشد. (نمونه معتبر 1401/01/01)'
                    });
                    return;
                }
                $("#loading").fadeIn();
                var day = $("#day").val();
                var month = $("#month").val();
                var from_birthday = $("#from-birthday").val();
                var to_birthday = $("#to-birthday").val();
                var data = {
                    "day": day,
                    "month": month,
                    "from_birthday": from_birthday,
                    "to_birthday": to_birthday,
                    "filter_search": true
                }
                window["data"] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });
            $(document.body).on("click", "#forward-birth", function() {
                $("#loading").fadeIn();
                var data = {
                    daysForward: $('#days-forward').val(),
                    filter_search: true
                }
                window.daysForward = data.daysForward;
                window["data"] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });


            $(document.body).on("click", ".send-sms", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از انجام این کار اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        var action = $(this).data("action");
                        var day = $("#day").val();
                        var month = $("#month").val();
                        var daysForward = window.daysForward;
                        var from_birthday = $("#from-birthday").val();
                        var to_birthday = $("#to-birthday").val();
                        $.ajax({
                            type: "POST",
                            url: "{{ route('birthdaySms') }}",
                            data: {
                                id: id,
                                action: action,
                                day: day,
                                month: month,
                                daysForward:daysForward,
                                from_birthday: from_birthday,
                                to_birthday: to_birthday
                            },
                            success: function(data) {
                                makeTable(window["data"]);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('موفق')",
                                    text: "@lang('با موفقیت انجام شد.')",
                                    icon: "success"
                                });
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('خطا')",
                                    text: data.respnoseJSON.message,
                                    icon: "succcess"
                                });
                            }
                        });
                    }

                });
            });

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudBirthday') }}",
                    success: function(data) {
                        $("#crud-result").html(data);
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-birthday", function() {
                $("#loading").fadeIn();
                var days = $("#days").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('storeBirthday') }}",
                    data: {
                        days: days
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            text: "@lang('با موفقیت انجام شد.')",
                            icon: "success"
                        });
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportPerson') }}",
                    type: "POST",
                    data: window["data"],
                    success: function() {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            icon: 'success',
                            text: "@lang('گزارش گیری انجام شد. از صفحه گزارشات اکسل میتوانید این گزارش را دانلود کنید.')"
                        });
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });
        });
    </script>
@stop
