@extends('parts.master')

@section('title', __('گزارش تحلیلی'))

@section('head-styles')
    <style>
        html.dark-style th,
        html.dark-style td {
            color: black !important;
        }
    </style>
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="mb-4">
                <div class="col">
                    <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                        aria-expanded="false" aria-controls="collapseExample">
                        <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">@lang('جستجو')</span>
                    </button>
                </div>
                <div class="collapse" id="collapseExample">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">@lang('از تاریخ')</label>
                                            <input type="text" id="from-date" class="form-control date-mask "
                                                placeholder="1400/01/01">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">@lang('تا تاریخ')</label>
                                            <input type="text" id="to-date" class="form-control date-mask "
                                                placeholder="1400/01/01">
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end justify-content-start">
                                        <div class="form-group">
                                            <button class="btn btn-success" id="search-report">@lang('جستجو')</button>
                                            <a href="{{ route('report') }}" class="btn btn-secondary">@lang('نمایش روز')</a>
                                            {{-- <button class="btn btn-info" id="show_all">@lang('نمایش همه')</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0">@lang('گزارش تحلیلی')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3" id="sectionTable">
                                {!! $sectionTable !!}
                            </div>
                            <div class="col-sm-4" id="paymentTypeTable">
                                {!! $paymentTypeTable !!}
                            </div>
                            <div class="col-sm-4" id="costTable">
                                {!! $costTable !!}
                            </div>

                        </div>
                    </div>
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

            $(document.body).on("click", "#search-report", function() {
                $("#loading").fadeIn();
                var from_date = $("#from-date").val();
                var to_date = $("#to-date").val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('searchReport') }}",
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                    },
                    success: function(data) {
                        $("#total-factors").html(data.total_factors);
                        $("#total-games").html(data.total_games);
                        $("#total-payments").html(data.total_payments);
                        $("#total-offered").html(data.total_offered);
                        $("#total-rounded").html(data.total_rounded);
                        $("#total-vat").html(data.total_vat);
                        $("#total-costs").html(data.total_costs);
                        $('#paymentTypeTable').html(data.payment_type_view);
                        $('#sectionTable').html(data.section_table);
                        $('#costTable').html(data.costTable);
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        console.log(data);
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
