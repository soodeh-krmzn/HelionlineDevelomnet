<ul class="nav nav-pills flex-column flex-md-row mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $page == 'global' ? 'active' : '' }} my-1 my-md-0" href="{{ route('global') }}"><i class="bx bx-user me-1"></i> {{ __('عمومی') }}</a>
    </li>
    @if (app()->getLocale()=='fa')
    <li class="nav-item">
        <a class="nav-link {{ $page == 'sms' ? 'active' : '' }}" href="{{ route('sms') }}"><i class="bx bx-envelope me-1"></i> {{ __('پیامک') }}</a>
    </li>
    @endif
    <li class="nav-item">
        <a class="nav-link {{ $page == 'price' ? 'active' : '' }}" href="{{ route('price') }}"><i class="bx bx-dollar me-1"></i> {{ __('تعرفه') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $page == 'personalize' ? 'active' : '' }}" href="{{ route('personalize') }}"><i class="bx bx-check-circle me-1"></i> {{ __('شخصی سازی') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $page == 'printSetting' ? 'active' : '' }}" href="{{ route('printSetting') }}"><i class="bx bx-check-circle me-1"></i> {{ __('تنظیمات چاپ') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $page == 'webServices' ? 'active' : '' }}" href="{{ route('webServices') }}"><i class="bx bx-check-circle me-1"></i> {{ __('وب سرویس ها') }}</a>
    </li>
</ul>
