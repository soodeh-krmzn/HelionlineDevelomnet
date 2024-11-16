@extends('parts.master')

@section('title', 'رتبه بندی اعضاء')

@section('head-styles')

@stop

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="collapse" id="collapseExample">
                <div class="card-header border-bottom">
                    <div class="py-3 primary-font">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از تاریخ') }}</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask" placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا تاریخ') }}</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask" placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-ratings">{{ __('جستجو') }}</button>
                                    <a href="{{ route("ratingClub") }}" class="btn btn-info" id="show-all">{{ __('نمایش همه') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> {{ __('جستجو') }}
                        </button>
                    </div>
                    <div class="col">
                        <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('footer-scripts')
    <script type="text/javascript">

        let table = `<div class="table-responsive">
        <table id="rating-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام')</th>
                    <th>@lang('امتیاز کل')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#rating-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableRatingClub') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'person_id'},
                    {data: 'sum'}
                ]
            });
        }

        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            makeTable();

            $(document.body).on("click", "#search-ratings", function() {
                $("#loading").fadeIn();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var data = {
                    from_date: from_date,
                    to_date: to_date,
                    filter_search: true
                }
                window['data'] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportRatingClub') }}",
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
