<li class="chat-contact-list-item chat-contact-list-item-title">
    <h5 class="text-primary mb-0 secondary-font">تیکت ها</h5>
</li>
@if ($tickets)
    @foreach ($tickets as $ticket)
        @php
            switch ($ticket->status) {
                case "closed":
                    $badge = "badge bg-dark";
                break;
                case "waiting-for-customer":
                    $badge = "badge bg-warning";
                break;
                default:
                    $badge = "badge bg-info";
                break;
            }
        @endphp
        <li class="chat-contact-list-item {{$ticket->id == $target ? 'active' : ''}}">
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
