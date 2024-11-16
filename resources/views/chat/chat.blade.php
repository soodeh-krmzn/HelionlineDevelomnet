@extends('parts.master')

@section('title', 'پشتیبانی')

@section('head-styles')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-chat.css') }}">
    <style>
    </style>
@stop

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-chat card overflow-hidden">
            <div class="row g-0">
                <!-- Sidebar Left -->
                <div class="col app-chat-sidebar-left app-sidebar overflow-hidden" id="app-chat-sidebar-left">
                    <div
                        class="chat-sidebar-left-user sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-4 pt-5">
                        <div class="avatar avatar-xl avatar-online">
                            <img src="../../assets/img/avatars/1.png" alt="آواتار" class="rounded-circle">
                        </div>
                        <h5 class="mt-2 mb-0">جان اسنو</h5>
                        <small>مدیر</small>
                        <i class="bx bx-x bx-sm cursor-pointer close-sidebar" data-bs-toggle="sidebar" data-overlay
                            data-target="#app-chat-sidebar-left"></i>
                    </div>
                    <div class="sidebar-body px-4 pb-4">
                        <div class="my-4">
                            <p class="text-muted text-uppercase">درباره</p>
                            <textarea id="chat-sidebar-left-user-about" class="form-control chat-sidebar-left-user-about mt-3" rows="4"
                                maxlength="120">
لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه</textarea>
                        </div>
                        <div class="my-4">
                            <p class="text-muted text-uppercase">@lang('وضعیت')</p>
                            <div class="d-grid gap-1">
                                <div class="form-check form-check-success">
                                    <input name="chat-user-status" class="form-check-input" type="radio" value="active"
                                        id="user-active" checked>
                                    <label class="form-check-label" for="user-active">فعال</label>
                                </div>
                                <div class="form-check form-check-danger">
                                    <input name="chat-user-status" class="form-check-input" type="radio" value="busy"
                                        id="user-busy">
                                    <label class="form-check-label" for="user-busy">مشغول</label>
                                </div>
                                <div class="form-check form-check-warning">
                                    <input name="chat-user-status" class="form-check-input" type="radio" value="away"
                                        id="user-away">
                                    <label class="form-check-label" for="user-away">دور</label>
                                </div>
                                <div class="form-check form-check-secondary">
                                    <input name="chat-user-status" class="form-check-input" type="radio" value="offline"
                                        id="user-offline">
                                    <label class="form-check-label" for="user-offline">آفلاین</label>
                                </div>
                            </div>
                        </div>
                        <div class="my-4">
                            <p class="text-muted text-uppercase">تنظیمات</p>
                            <ul class="list-unstyled d-grid gap-3 me-3">
                                <li class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bx bx-message-square-detail me-1"></i>
                                        <span class="align-middle">اعتبارسنجی دو مرحله‌ای</span>
                                    </div>
                                    <label class="switch switch-primary me-4">
                                        <input type="checkbox" class="switch-input" checked>
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                    </label>
                                </li>
                                <li class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bx bx-bell me-1"></i>
                                        <span class="align-middle">اعلان</span>
                                    </div>
                                    <label class="switch switch-primary me-4">
                                        <input type="checkbox" class="switch-input">
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                    </label>
                                </li>
                                <li>
                                    <i class="bx bx-user me-1"></i>
                                    <span class="align-middle">دعوت دوستان</span>
                                </li>
                                <li>
                                    <i class="bx bx-trash me-1"></i>
                                    <span class="align-middle">حذف حساب</span>
                                </li>
                            </ul>
                        </div>
                        <div class="d-flex mt-4">
                            <button class="btn btn-primary" data-bs-toggle="sidebar" data-overlay
                                data-target="#app-chat-sidebar-left">
                                خروج
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /Sidebar Left-->

                <!-- Chat & Contacts -->
                <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end"
                    id="app-chat-contacts">
                    <div class="sidebar-header py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar avatar-online me-3" data-bs-toggle="sidebar"
                                data-overlay="app-overlay-ex" data-target="#app-chat-sidebar-left">
                                <img class="user-avatar rounded-circle cursor-pointer"
                                    src="{{ (new \App\Models\Setting())->getSetting('avatar') != '' ? (new \App\Models\Setting())->getSetting('avatar') : asset('assets/img/default-avatar.png') }}"
                                    alt="آواتار">
                            </div>
                            <div class="w-100 align-items-left" style="text-align: left">
                                <button id="new-ticket" class="btn btn-sm btn-primary rounded-pill"><i
                                        class="bx bx-plus fs-4"></i>تیکت جدید
                                </button>
                            </div>
                        </div>
                        <i id="close-side"
                            class="bx bx-x cursor-pointer position-absolute top-0 end-0 mt-2 me-1 fs-4 d-lg-none d-block"
                            data-overlay data-bs-toggle="sidebar" data-target="#app-chat-contacts"></i>
                    </div>
                    <div class="sidebar-body">
                        <!-- Chats -->
                        <ul class="list-unstyled chat-contact-list" id="chat-list">
                            <li class="chat-contact-list-item chat-contact-list-item-title">
                                <h5 class="text-primary mb-0 secondary-font">تیکت ها</h5>
                            </li>
                            @if ($tickets->count() > 0)
                                @foreach ($tickets as $ticket)
                                    @php
                                        switch ($ticket->status) {
                                            case 'closed':
                                                $badge = 'badge bg-dark';
                                                break;
                                            case 'waiting-for-customer':
                                                $badge = 'badge bg-success';
                                                break;
                                            default:
                                                $badge = 'badge bg-info';
                                                break;
                                        }
                                    @endphp
                                    <li class="chat-contact-list-item {{ $loop->first ? 'active' : '' }}">
                                        <a class="d-flex align-items-center ticket-link" data-id="{{ $ticket->id }}">
                                            {{-- <div class="flex-shrink-0 avatar avatar-offline">
                                        <img src="../../assets/img/avatars/2.png" alt="آواتار" class="rounded-circle">
                                        </div> --}}
                                            <div class="chat-contact-info flex-grow-1">
                                                <h6 class="chat-contact-name text-truncate m-0">{{ $ticket->subject }}</h6>
                                                <p class="m-0 mt-2 {{ $badge }}">
                                                    <span class="">وضعیت: @lang($ticket->status)</span>
                                                </p>
                                            </div>
                                            <small class="text-muted mb-auto">{{ $ticket->lastMsgTime() }}</small>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="chat-contact-list-item chat-list-item-0">
                                    <h6 class="text-muted mb-0">گفتگویی پیدا نشد</h6>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>
                <!-- /Chat contacts -->


                <!-- Chat ------------------------ History -->
                @php
                    $ticket = $tickets->first();
                @endphp
                <div id="chat-root" class="col app-chat-history bg-body">
                    <div class="chat-history-wrapper">
                        <div class="chat-history-header border-bottom {{ $ticket ? '' : 'd-lg-none' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex overflow-hidden align-items-center">
                                    <i class="bx bx-menu bx-sm cursor-pointer d-lg-none d-block me-2"
                                        data-bs-toggle="sidebar" data-overlay data-target="#app-chat-contacts"></i>
                                    {{-- <div class="flex-shrink-0 avatar">
                                        <img src="../../assets/img/avatars/2.png" alt="آواتار" class="rounded-circle"
                                            data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right">
                                    </div> --}}
                                    @if ($ticket)
                                        <div class="chat-contact-info flex-grow-1 ms-3">
                                            <h6 class="m-0">{{ $ticket->subject }}</h6>
                                            <small class="user-status text-muted">وضعیت:
                                                {{ __($ticket->status) }}</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center">

                                </div>
                            </div>
                        </div>
                        @if ($ticket)
                            @php
                                $ticket
                                    ->Chats()
                                    ->where('account_id', '0')
                                    ->update([
                                        'seen' => 1,
                                    ]);
                            @endphp
                            <div class="chat-history-body bg-body" style="overflow-y: scroll">
                                <ul class="list-unstyled chat-history mb-0">
                                    @foreach ($ticket->chats as $chat)
                                        @php
                                            $rel = $chat->account_id == 0 ? 'admin' : 'client';
                                        @endphp
                                        @if ($rel == 'admin')
                                            <li class="chat-message chat-message-right">
                                                <div class="d-flex overflow-hidden">
                                                    @if ($file = $chat->getFile())
                                                        <div class="user-avatar flex-shrink-0 me-3">
                                                            <a href="{{ $file }}" target="_blank"
                                                                class="btn btn-primary btn-sm p-2  rounded-pill">
                                                                <i class="fas fa-download me-1"></i> {{ __('فایل') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <div class="chat-message-wrapper flex-grow-1">
                                                        <div class="text-start text-muted mt-1">
                                                            <small>{{ $chat->admin?->getFullName()??$chat->user_id }}</small>
                                                        </div>
                                                        <div class="chat-message-text">
                                                            <p class="mb-0">{!! $chat->body !!}</p>
                                                        </div>
                                                        <div class="text-muted mt-1">
                                                            <small>{{ timeFormat($chat->created_at) }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @else
                                            <li class="chat-message">
                                                <div class="d-flex overflow-hidden">
                                                    <div class="chat-message-wrapper flex-grow-1">
                                                        <div class="text-start text-muted mt-1">
                                                            <small>{{ $chat->user?->getFullName() }}</small>
                                                        </div>
                                                        <div class="chat-message-text">
                                                            <p class="mb-0">{{ $chat->body }}</p>
                                                        </div>
                                                        <div class="text-end text-muted mt-1">
                                                            <i
                                                                class="bx bx-check-double {{ $chat->seen ? 'text-success' : 'text-secondery' }}"></i>
                                                            <small>{{ timeFormat($chat->created_at) }}</small>
                                                        </div>
                                                    </div>
                                                    @if ($file = $chat->getFile())
                                                        <div class="user-avatar flex-shrink-0 me-3">
                                                            <a href="{{ $file }}" target="_blank"
                                                                class="btn btn-primary btn-sm p-2  rounded-pill">
                                                                <i class="fas fa-download me-1"></i> {{ __('فایل') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Chat message form -->
                            @if ($ticket->status != 'closed')
                                <div class="chat-history-footer shadow-sm">
                                    <form id="new-body-form"
                                        class="form-send-message d-flex justify-content-between align-items-center">
                                        <input name="body" class="form-control message-input border-0 me-3 shadow-none"
                                            placeholder="پیام خود را اینجا بنویسید">
                                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                        <div class="message-actions d-flex align-items-center">
                                            <label for="attach-doc" class="form-label mb-0">
                                                <i class="bx bx-paperclip bx-sm cursor-pointer mx-3"></i>
                                                <input type="file" name="file" id="attach-doc" hidden>
                                            </label>
                                            <button id="add-chat" class="btn btn-primary d-flex send-msg-btn">
                                                <i class="bx bx-paper-plane me-md-1 me-0"></i>
                                                <span class="align-middle d-md-inline-block d-none">ارسال</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>

                </div>
                <!-- /Chat History -->


                <div class="app-overlay"></div>
            </div>
        </div>
    </div>
    <!--/ Content -->
    {{-- hiddens --}}
    {{-- tikcet form --}}
    <div id="new-ticket-form-content" class="d-none">
        <div class="chat-history-header border-bottom ">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex overflow-hidden align-items-center">
                    <i class="bx bx-menu bx-sm cursor-pointer d-lg-none d-block me-2" data-bs-toggle="sidebar"
                        data-overlay data-target="#app-chat-contacts"></i>
                    {{-- <div class="flex-shrink-0 avatar">
                        <img src="../../assets/img/avatars/2.png" alt="آواتار" class="rounded-circle"
                            data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right">
                    </div> --}}
                    <div class="chat-contact-info flex-grow-1 ms-3">
                        <h6 class="m-0">ایجاد تیکت</h6>
                        {{-- <small class="user-status text-muted">وضعیت تیکت: درحال بررسی</small> --}}
                    </div>
                </div>
                <div class="d-flex align-items-center">

                </div>
            </div>
        </div>

        <form class="mt-3 new-ticket-form" action="#">
            <div class="row px-3">
                <div class="col-12 mb-2">
                    <div class="form-group">
                        <label for="">موضوع تیکت را وارد کنید</label>
                        <input type="text" name="subject" class="form-control">
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <div class="form-group">
                        <label for="">متن تیکت را وارد کنید</label>
                        <textarea class="form-control" name="body" rows="5"></textarea>
                    </div>
                </div>
                <div class="col-12 mb-2 row">
                    <div class="col">
                        {{-- <div class="form-group col-md-3">
                        <label for="primary_image"> انتخاب تصویر اصلی </label>
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input"
                                id="primary_image">
                            <label class="custom-file-label" for="primary_image"> انتخاب فایل </label>
                        </div>
                    </div> --}}
                        <div class="custom-file">
                            <input type="file" name="file" class="custom-file-input" id="customFile">
                            {{-- <label class="custom-file-label" for="customFile">Choose file</label> --}}
                        </div>
                        {{-- <label for="attach-doc" class="form-label mb-0">
                        <i class="bx bx-paperclip bx-sm cursor-pointer mx-3"></i>
                        <input type="file" name="attach" id="attach-doc" hidden>
                    </label> --}}
                    </div>
                    <div class="col">
                        <button class="submit-new-ticket btn btn-primary d-flex send-msg-btn">
                            <i class="bx bx-paper-plane me-md-1 me-0"></i>
                            <span class="align-middle d-md-inline-block d-none">ارسال</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@stop

@section('footer-scripts')
    <!-- Page JS -->
    <script src="{{ asset('assets/js/app-chat.js') }}"></script>


    <script>
        $(document.body).on("submit", "#chat-root .new-ticket-form", function(event) {
            event.preventDefault();
        });
        $(document.body).on("submit", "#new-body-form", function(event) {
            event.preventDefault();
        });

        // opne chats of a ticekt
        $(document.body).on("click", ".ticket-link", function() {
            data = {
                id: $(this).attr('data-id'),
            }
            $('.ticket-link').parent().removeClass('active');
            $(this).parent().addClass('active');
            viewAjax("/get-chat", data, '#chat-root');

        });
        // end

        // submit a new ticket
        $(document.body).on("click", "#chat-root .submit-new-ticket", function() {
            form = $('#chat-root .new-ticket-form');
            var formData = new FormData(form[0]);
            formDataAjax('/new-ticket', formData, '{{ __('موفق') }}', '{{ __('تیکت شما ثبت شد') }}', function(
                response) {
                data = {
                    id: response,
                }
                viewAjax("/get-chat", data, '#chat-root');
                viewAjax("/get-chat-list", data, '#chat-list');
                //menu

            });
        });
        //end

        //open new ticket form
        $('#new-ticket').on('click', function() {
            root = $('#chat-root');
            root.fadeOut('slow', function() {
                root.html($('#new-ticket-form-content').html());
                setTimeout(() => {
                    root.fadeIn(1000);
                }, 1000);

            });
        });
        //end

        // add chat to ticket
        $(document.body).on("click", "#add-chat", function() {
            let form = $('#new-body-form');
            let formData = new FormData(form[0]);
            formData.append('action', 'new-chat');
            formDataAjax('/new-ticket', formData, false, false, function(response) {
                data = {
                    id: response,
                }
                viewAjax("/get-chat", data, '#chat-root');
                viewAjax("/get-chat-list", data, '#chat-list');
            })
        });
    </script>

@stop
