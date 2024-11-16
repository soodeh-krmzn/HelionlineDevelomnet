@extends('parts.master')

@section('title', __('راه اندازی اولیه'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}">
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-12">
                <h5 class="secondary-font">@lang('نصب و راه اندازی اولیه نرم افزار')</h5>
            </div>

            <div class="col-12 mb-4">
                <div class="bs-stepper wizard-vertical vertical wizard-numbered mt-2">
                    <div class="bs-stepper-header">
                        <div class="step active" data-target="#tab1">
                            <button type="button" class="step-trigger" aria-selected="true">
                                <span class="bs-stepper-circle">1</span>
                                <span class="bs-stepper-label">@lang('اطلاعات اولیه')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab2">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">2</span>
                                <span class="bs-stepper-label">@lang('بخش ها')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab3">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">3</span>
                                <span class="bs-stepper-label">@lang('تعرفه ها')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab4">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">4</span>
                                <span class="bs-stepper-label">@lang('روش های پرداخت')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab5">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">5</span>
                                <span class="bs-stepper-label">@lang('تعاریف')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab6">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">6</span>
                                <span class="bs-stepper-label">@lang('ورود مشتری')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab7">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">7</span>
                                <span class="bs-stepper-label">@lang('خروج مشتری')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab8">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">8</span>
                                <span class="bs-stepper-label">@lang('گزارشات')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab9">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">9</span>
                                <span class="bs-stepper-label">@lang('سایر امکانات')</span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#tab10">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle">10</span>
                                <span class="bs-stepper-label">@lang('راهنما و پشتیبانی')</span>
                            </button>
                        </div>
                    </div>

                    <div class="bs-stepper-content">
                        <form onsubmit="return false">

                            <div id="tab1" class="content active dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('اطلاعات اولیه')</h6>
                                    <small>@lang('نام مجموعه، توضیحات، لوگو و') ...</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('برای شروع نیازه که اطلاعات مجموعه شامل نام، نشانی، طراحی فرم های چاپی، لوگو و ... رو تکمیل کنید.')
                                                            <br>
                                                            @lang('برای انجام این کار کافیه وارد منوی پیکربندی و سپس عمومی بشید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev" disabled="">
                                            <!--i class="bx bx-chevron-left bx-sm ms-sm-n2"></i-->
                                            <span class="d-sm-inline-block d-none"></span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab2" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('بخش ها')</h6>
                                    <small>@lang('بخش های متنوع مجموعه شما')</small>
                              </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('تو این مرحله نیازه که بخش های مختلف مجموعه تون رو تعریف کنید.')
                                                            <br>
                                                            @lang('به عنوان مثال ممکنه شما خانه بادی، کلبه شنی و خانه مشاغل، واقعیت مجازی، بازی های فکری و ... رو داشته باشید.')
                                                            <br>
                                                            @lang('برای تکمیل این بخش وارد منوی تعاریف و سپس بخش ها بشید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab3" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('تعرفه ها')</h6>
                                    <small>@lang('قیمت گذاری ارائه خدمات شما')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('تو این مرحله باید تعرفه استفاده از خدمات مجموعه رو تعریف کنید.')
                                                            <br>
                                                            @lang('تو نرم افزار ما امکان تعریف انواع قیمت گذاری ها وجود داره.')
                                                            <br>
                                                            @lang('برای انجام این کار کافیه وارد منوی پیکربندی و سپس تعرفه بشید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab4" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('روش های پرداخت')</h6>
                                    <small>@lang('شیوه های تسویه حساب و دریافت وجه از مشتری')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('خب تو این مرحله باید روش های پرداخت رو تعریف کنید.')
                                                            <br>
                                                            @lang('معمولا تو اکثر مجموعه ها چندین کارتخوان و صندوق وجود داره.')
                                                            <br>
                                                            @lang('برای تعریف این موارد وارد منوی پیکربندی و سپس روش های پرداخت بشید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab5" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('تعاریف')</h6>
                                    <small>@lang('اطلاعات پایه نرم افزار')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('یکی از اصلی ترین منوهای برنامه منوی تعاریف است.')
                                                            <br>
                                                            @lang('تو این منو شما می تونید انواع لیست داده های قابل تعریف رو ببینید.')
                                                            <br>
                                                            @lang('اشخاص، محصولات، شمارنده، گروه بندی و ... از مهم ترین موارد این منو هستند.')
                                                            <br>
                                                            @lang('حتما یه سر به این منو بزنید و اطلاعاتش رو تکمیل کنید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab6" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('ورود مشتری')</h6>
                                    <small>@lang('شروع ارتباط با مشتری')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('کلیدی ترین بخش نرم افزار بخش ورود مشتری است.')
                                                            <br>
                                                            @lang('کافیه وارد داشبرد برنامه بشید و کلید ورود رو بزنید.')
                                                            <br>
                                                            @lang('یک فرم براتون باز میشه که باید اطلاعات مشتری رو وارد و ثبت کنید.')
                                                            <br>
                                                            @lang('پس از ثبت ورود، مشتری به لیست افراد حاضر در مجموعه اضافه میشه.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab7" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('خروج مشتری')</h6>
                                    <small>@lang('ثبت خروج و تسویه حساب')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('زمانی که مشتری قصد خروج از مجموعه رو داشته باشد، فقط کافیه اسمش رو از لیست افراد حاضر پیدا کنید و صورت حسابش رو باز کنید.')
                                                            <br>
                                                            @lang('می تونید صورتحسابش رو چاپ یا با پیامک براش ارسال کنید، جمع حسابش رو ببینید و تسویه بزنید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab8" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('گزارشات')</h6>
                                    <small>@lang('انواع نمودار و گزارشات مدیریتی')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('پس از ثبت کامل اطلاعات ورود و خروج مشتری ها، سیستم به شما کلی گزارش و نمودار کاربردی و جذاب میده.')
                                                            <br>
                                                            @lang('کافیه وارد منوی گزارشات بشید و بر اساس نیاز گزارش مد نظرتون رو انتخاب و مشاهده کنید.')
                                                            <br>
                                                            @lang('حتی می تونید از همه گزارشات نسخه چاپی و خروجی اکسل تهیه کنید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab9" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('سایر امکانات')</h6>
                                    <small>این همه ماجرا نبود...</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('هلی سافت کلی امکانات جذاب و کاربردی دیگه هم داره.')
                                                            <br>
                                                            @lang('مثل نظرسنجی، باشگاه مشتریان، سیستم حسابداری برای مدیریت هزینه ها و ...')
                                                            <br>
                                                            @lang('که می تونید همه این موارد رو داخل منوهای نرم افزار ببینید و باهاشون کار کنید.')
                                                        </p>
                                                    </div>

                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button class="btn btn-primary btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('بعدی')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="tab10" class="content dstepper-block">
                                <div class="content-header mb-3">
                                    <h6 class="mb-1">@lang('راهنما و پشتیبانی')</h6>
                                    <small>@lang('تو این مسیر کنارتون هستیم...')</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="mb-3 col-12 mb-0">
                                                    <div class="alert alert-warning">
                                                        <!--h6 class="alert-heading mb-1">تکمیل اطلاعات مجموعه</h6-->
                                                        <p class="mb-0">
                                                            @lang('همه صفحات نرم افزار یک آموزش ویدئویی هم دارند. کافیه داخل صفحه مورد نظر دکمه ؟ رو بزنید.')
                                                            <br>
                                                            @lang('اگه نیاز به کمک، آموزش یا راهنمایی بیشتری داشتید کافیه به ما زنگ بزنید یا به پشتیبان پیام بدید.')
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        @lang('ویدئو کامل آموزش مربوط به امکانات ذکر شده، در هر صفحه با کلیک روی دکمه ؟ در دسترس شماست.')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-label-secondary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="d-sm-inline-block d-none">@lang('قبلی')</span>
                                        </button>
                                        <button id="store-setup" class="btn btn-success btn-next">
                                            <span class="d-sm-inline-block d-none me-sm-1">@lang('تایید نهایی و راه اندازی نرم افزار')</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-validation.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on("click", "#store-setup", function () {
                $("#loading").fadeIn();
                $.ajax({
                    type: "POST",
                    url: "{{ route('storeSetup') }}",
                    data: {
                        action: "done"
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('عملیات موفق')",
                            text: "@lang('عملیات راه اندازی نرم افزار با موفقیت انجام شد.')",
                            icon: "success",
                            timer: 3000
                        });
                        window.location.href = "{{ route('dashboard') }}";
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
