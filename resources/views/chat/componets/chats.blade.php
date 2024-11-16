<div class="chat-history-wrapper">
    <div class="chat-history-header border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex overflow-hidden align-items-center">
                <i class="bx bx-menu bx-sm cursor-pointer d-lg-none d-block me-2" data-bs-toggle="sidebar" data-overlay
                    data-target="#app-chat-contacts"></i>
                {{-- <div class="flex-shrink-0 avatar">
                    <img src="../../assets/img/avatars/2.png" alt="آواتار" class="rounded-circle"
                        data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right">
                </div> --}}
                <div class="chat-contact-info flex-grow-1 ms-3">
                    <h6 class="m-0">{{ $ticket->subject }}</h6>
                    <small class="user-status text-muted">وضعیت:
                        {{ __($ticket->status) }}
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center">

            </div>
        </div>
    </div>
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
                                    <p class="mb-0">{!! $chat->body !!}</p>
                                </div>
                                <div class="text-end text-muted mt-1">
                                    <i class="bx bx-check-double {{ $chat->seen ? 'text-success' : 'text-secondery' }}"></i>
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
            <form id="new-body-form" class="form-send-message d-flex justify-content-between align-items-center">
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
</div>
<!-- Helpers -->
<script>
    var myObject = {
        initSidebarToggle: function() {
            $('[data-bs-toggle="sidebar"]').each(function() {
                $(this).on("click", function() {
                    var targetSelector = $(this).attr("data-target");
                    var overlayAttr = $(this).attr("data-overlay");
                    var overlays = $(".app-overlay");

                    $(targetSelector).each(function() {
                        $(this).toggleClass("show");

                        if (overlayAttr !== "false" && overlays.length > 0) {
                            overlays.eq(0).toggleClass("show");

                            overlays.eq(0).on("click", function(e) {
                                $(this).removeClass("show");
                                $(targetSelector).removeClass("show");
                            });
                        }
                    });
                });
            });
        }
    };
    myObject.initSidebarToggle();
    $('#close-side').on('click',function() {
        $($(this).attr("data-target")).removeClass('show');
        $(".app-overlay").removeClass('show');
    })

</script>
