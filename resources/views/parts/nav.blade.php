<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="/" class="app-brand-link gap-2">
                <img src="{{ asset('assets/img/logo.png') }}" class="img-fluid">
                <span class="app-brand-text demo menu-text fw-bold">{{ __('هلی آنلاین') }}</span>
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="bx bx-x bx-sm align-middle"></i>
            </a>
        </div>

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>
        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <label class="switch switch-success switch-lg mb-1" style="margin-left:4rem">
                    <input type="checkbox" class="switch-input status-offline">
                    <span class="switch-toggle-slider">
                        <span class="switch-on">
                            <i class="bx bx-check"></i>
                        </span>
                        <span class="switch-off">
                            <i class="bx bx-x"></i>
                        </span>
                    </span>
                </label>
                <li class="nav-item  me-2">
                    <a href="{{ route('chat') }}">
                        <span class="badge bg-warning">
                            <i class="bx bx-support"></i>
                            {{ __('پشتیبانی') }}
                        </span>
                        <span
                            class="badge bg-success rounded-pill badge-notifications">{{ auth()->user()->account->unreadTickets() }}</span>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a href="https://helisystem.ir/payment/create/{{ auth()->user()->username }}/account">
                        <span
                            class="badge bg-{{ (new \App\Models\Setting())->getSetting('charge') <= 7 ? 'danger' : 'success' }}">
                            <i class="bx bxs-battery-low"></i>
                            {{ (new \App\Models\Setting())->getDaysLeft() }}
                        </span>
                    </a>
                </li>
                @if ((new \App\Models\Setting())->getSetting('sms_panel') == false)
                    <li class="nav-item me-2">
                        <a href="https://helisystem.ir/payment/create/{{ auth()->user()->username }}/sms">
                            <span
                                class="badge bg-{{ (new \App\Models\Setting())->getSetting('sms_charge') <= 100 ? 'danger' : 'info' }}">
                                <i class="bx bxs-envelope"></i>
                                {{ cnf((new \App\Models\Setting())->getSetting('sms_charge')) }}
                            </span>
                        </a>
                    </li>
                @endif
                <!-- Style Switcher -->
                {{-- <li class="nav-item me-2 me-xl-0">
                    <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                        <i class="bx bx-sm"></i>
                    </a>
                </li> --}}
                <!--/ Style Switcher -->

                {{-- <!-- Quick links  -->
                <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bx bx-grid-alt bx-sm"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto secondary-font">میانبرها</h5>
                                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="افزودن میانبر"><i class="bx bx-sm bx-plus-circle"></i></a>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container">
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-calendar fs-4"></i>
                          </span>
                                    <a href="app-calendar.html" class="stretched-link">تقویم</a>
                                    <small class="text-muted mb-0">قرارهای ملاقات</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-food-menu fs-4"></i>
                          </span>
                                    <a href="app-invoice-list.html" class="stretched-link">برنامه صورتحساب</a>
                                    <small class="text-muted mb-0">مدیریت حساب‌ها</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-user fs-4"></i>
                          </span>
                                    <a href="app-user-list.html" class="stretched-link">برنامه کاربر</a>
                                    <small class="text-muted mb-0">مدیریت کاربران</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-check-shield fs-4"></i>
                          </span>
                                    <a href="app-access-roles.html" class="stretched-link">مدیریت نقش‌‌ها</a>
                                    <small class="text-muted mb-0">مجوزها</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-pie-chart-alt-2 fs-4"></i>
                          </span>
                                    <a href="index.html" class="stretched-link">داشبورد</a>
                                    <small class="text-muted mb-0">پروفایل کاربر</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-cog fs-4"></i>
                          </span>
                                    <a href="pages-account-settings-account.html" class="stretched-link">تنظیمات</a>
                                    <small class="text-muted mb-0">تنظیمات حساب</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-help-circle fs-4"></i>
                          </span>
                                    <a href="pages-help-center-landing.html" class="stretched-link">مرکز راهنمایی</a>
                                    <small class="text-muted mb-0">سوالات متداول و مقالات</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                          <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                            <i class="bx bx-window-open fs-4"></i>
                          </span>
                                    <a href="modal-examples.html" class="stretched-link">مودال‌ها</a>
                                    <small class="text-muted mb-0">پاپ‌آپ‌های کاربردی</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </li> --}}
                <!-- Quick links -->

                <!-- Notification -->
                <li class="nav-item me-3 me-xl-2">
                    <a class="nav-link hide-arrow" href="{{ route('birthdayPerson') }}" aria-expanded="false">
                        <i class="bx bxs-cake fa-lg"></i>
                        <span
                            class="badge bg-danger rounded-pill badge-notifications">{{ \App\Models\Person::birthdayCount() }}</span>
                    </a>
                </li>
                <!--/ Notification -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ (new \App\Models\Setting())->getSetting('avatar') != '' ? (new \App\Models\Setting())->getSetting('avatar') : asset('assets/img/default-avatar.png') }}"
                                alt class="rounded-circle" style="object-fit: contain;">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ (new \App\Models\Setting())->getSetting('avatar') != '' ? (new \App\Models\Setting())->getSetting('avatar') : asset('assets/img/default-avatar.png') }}"
                                                alt class="rounded-circle" style="object-fit: contain;">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span
                                            class="fw-semibold d-block">{{ Auth::user()->name . ' ' . Auth::user()->family }}</span>
                                        <small>{{ (new \App\Models\Setting())->getSetting('center_name') }}</small>-
                                        <small>{{ auth()->user()->account->package?->name }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="nav-link style-switcher-toggle hide-arrow text-center" href="javascript:void(0);">
                                @lang('حالت روز و شب')<i class="bx bx-sm mx-2"></i>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('myPayment') }}">
                                <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                                <span class="align-middle">{{ __('سوابق پرداخت') }}</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('faq') }}">
                                <i class="bx bx-help-circle me-2"></i>
                                <span class="align-middle">{{ __('سوالات متداول') }}</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a target="_blank" class="dropdown-item" href="{{ route('download_backup') }}">
                                <i class="bx bx-download me-2"></i>
                                <span class="align-middle">{{ __('دانلود نسخه پشتیبان') }}</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">{{ __('خروج') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>

        <!-- Search Small Screens -->
        <div class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
            <input type="text" class="form-control search-input border-0" placeholder="{{ __('جستجو') }} ..."
                aria-label="{{ __('جستجو') }}...">
            <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
        </div>
    </div>
</nav>
<!--/ Navbar -->