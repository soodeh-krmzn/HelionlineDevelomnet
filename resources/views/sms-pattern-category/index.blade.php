@extends('parts.master')

@section('title', 'دسته بندی الگوهای پیامک')

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1">
                    <div class="col-12">
                        <div class="mt-3 mb-3 dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <form action="{{ route("exportSection") }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary buttons-collection dropdown-toggle btn-label-secondary" tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog" aria-expanded="false">
                                            <span><i class="bx bx-upload me-2"></i>برون‌بری</span>
                                            <span class="dt-down-arrow"></span>
                                        </button>
                                    </form>
                                </div>
                                <button type="button" class="btn add-new btn-primary ms-2 mb-sm-0 crud" data-action="create" data-id="0" data-bs-target="#crud-modal" data-bs-toggle="modal">
                                    <span>افزودن دسته جدید</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="result">{{ $smsPatternCategory->showIndex() }}</div>
            </div>
        </div>

        <div class="modal fade" id="crud-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div id="crud-result"></div>
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

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudSmsPatternCategory') }}",
                    data:{
                        id: id,
                        action: action
                    },
                    success:function(data) {
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

            $(document.body).on("click", "#store-sms-pattern-category", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var name = $("#name").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeSmsPatternCategory') }}",
                        data:{
                            action: action,
                            id: id,
                            name: name
                        },
                        success: function(data) {
                            $("#result").html(data);
                            $("#name").val("");
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: `اطلاعات با موفقیت ${action == "create" ? "ثبت" : "ویرایش"} شد.`,
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
                }
            });

            $(document.body).on("click", ".delete-sms-pattern-category", function() {
                Swal.fire({
                      title: "@lang('اطمینان دارید؟')",
                    text: "@lang('آیا از حذف این مورد اطمینان دارید؟')",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "@lang('بله، مطمئنم.')",
                    cancelButtonText: "@lang('نه، پشیمون شدم.')"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteSmsPatternCategory') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                $("#result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                    text: "دسته با موفقیت حذف شد.",
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
                    }
                });
            });

        });
    </script>
@stop
