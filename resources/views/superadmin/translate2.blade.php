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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('نام')</label>
                                    <input type="text" id="s_name" class="form-control" placeholder="نام...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('نام خانوادگی')</label>
                                    <input type="text" id="s_family" class="form-control" placeholder="نام خانوادگی...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">موبایل</label>
                                    <input type="text" id="s_mobile" class="form-control just-numbers" placeholder="موبایل...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">کد ملی</label>
                                    <input type="text" id="s_national_code" class="form-control just-numbers" placeholder="کد ملی...">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">جنسیت</label>
                                    <select id="s_gender" class="form-select">
                                        <option value="">@lang('همه')</option>
                                        <option value="0">دختر</option>
                                        <option value="1">پسر</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">کد اشتراک</label>
                                    <input type="text" id="s_reg_code" class="form-control" placeholder="کد اشتراک...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">از تاریخ تولد</label>
                                    <input type="text" id="s_from_birth" class="form-control date-mask " placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">تا تاریخ تولد</label>
                                    <input type="text" id="s_to_birth" class="form-control date-mask " placeholder="1400/01/01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-end">
                            <div class="form-group">
                                <button class="btn btn-success" id="search-person">@lang('جستجو')</button>
                                <a href="{{ route("person") }}" class="btn btn-info" id="show-all">@lang('نمایش همه')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i>
                            <span class="d-none d-lg-inline-block">@lang('جستجو')</span>
                        </button>
                    </div>
                    <div class="col">
                        <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                </div>
                                @can('create')
                                    <button class="btn btn-secondary add-new btn-primary crud ms-2" data-action="create" data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">مشتری جدید</span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
                </div>
            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser">
                <div class="offcanvas-header border-bottom">
                    <h6 class="offcanvas-title" id="offcanvas-title"></h6>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0">
                    <div id="crud-result"></div>
                </div>
            </div>

            <div class="modal fade" id="meta" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content p-3 p-md-5">
                        <div id="meta-result"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('footer-scripts')

@stop
