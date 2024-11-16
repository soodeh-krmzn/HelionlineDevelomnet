@extends('parts.master')

@section('title', 'سوالات متداول')

@section('head-styles')
    <!-- Page CSS -->
    <link rel="stylesheet" href="../../assets/vendor/css/pages/page-faq.css">
@stop

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!--div class="faq-header d-flex flex-column justify-content-center align-items-center">
                <h3 class="text-center zindex-1 secondary-font">سلام، چطور می‌توانیم کمکتان کنیم؟</h3>
                <div class="input-wrapper my-3 input-group input-group-merge zindex-1">
                    <span class="input-group-text" id="basic-addon1"><i class="bx bx-search-alt bx-xs text-muted"></i></span>
                    <input type="text" class="form-control form-control-lg" placeholder="یک سوال جستجو کنید ..." aria-label="Search" aria-describedby="basic-addon1">
                </div>
                <p class="text-center text-body zindex-1 mb-0 px-3">
                    یا یک دسته برای یافتن سریع کمکی که نیاز دارید انتخاب کنید
                </p>
            </div-->

        <div class="row mt-4">
            <!-- Navigation -->
            <div class="col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
                <div class="d-flex justify-content-between flex-column mb-2 mb-md-0">
                    <ul class="nav nav-align-left nav-pills flex-column lh-1-85">
                        @foreach ($components as $component)
                        <li class="nav-item">
                            <button class="nav-link {{$loop->first?'active':''}}" data-bs-toggle="tab" data-bs-target="#component-{{$component->id}}">
                                <i class="bx bx-cog faq-nav-icon me-2"></i>
                                <span class="align-middle">{{$component->name}}  </span>
                            </button>
                        </li>
                        @endforeach
                        {{-- <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#payment">
                                <i class="bx bx-credit-card faq-nav-icon me-2"></i>
                                <span class="align-middle">@lang('پرداخت')</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#delivery">
                                <i class="bx bx-shopping-bag faq-nav-icon me-2"></i>
                                <span class="align-middle">ارسال</span>
                            </button>
                        </li> --}}
                        {{-- <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancellation">
                                <i class="bx bx-rotate-left faq-nav-icon me-2"></i>
                                <span class="align-middle">انصراف و مرجوعی</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#orders">
                                <i class="bx bx-cube faq-nav-icon me-2"></i>
                                <span class="align-middle">سفارشات من</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#product">
                                <i class="bx bx-cog faq-nav-icon me-2"></i>
                                <span class="align-middle">محصول و خدمات</span>
                            </button>
                        </li> --}}
                    </ul>
                    <div class="d-none d-md-block">
                        <div class="mt-5">
                            <img src="../../assets/img/illustrations/boy-working-light.png" class="img-fluid scaleX-n1"
                                alt="FAQ Image" data-app-light-img="illustrations/boy-working-light.png"
                                data-app-dark-img="illustrations/boy-working-dark.png">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Navigation -->

            <!-- FAQ's -->
            <div class="col-lg-9 col-md-8 col-12">
                <div class="tab-content py-0">
                    @foreach ($components as $component)
                    <div class="tab-pane fade {{$loop->first?'show active':''}}" id="component-{{$component->id}}" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div>
                                <span class="badge bg-label-primary rounded-2 p-2 mt-1">
                                    <i class="bx bx-credit-card fs-3 lh-1"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">
                                    <span class="align-middle">{{$component->name}}</span>
                                </h5>
                                {{-- <span class="lh-1-85">دریافت راهنمایی برای پرداخت</span> --}}
                                <span class="lh-1-85">{{$component->description}}</span>
                            </div>
                        </div>
                        <div id="accordionPayment" class="accordion accordion-header-primary">
                            @foreach ($component->questions as $question)
                            <div class="card accordion-item {{$loop->first?'active':''}}">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        aria-expanded="true" data-bs-target="#accordionPayment-{{$question->id}}"
                                        aria-controls="accordionPayment-{{$question->id}}">
                                       {{$question->title}}
                                    </button>
                                </h2>
                                <div id="accordionPayment-{{$question->id}}" class="accordion-collapse collapse {{$loop->first?'show':''}}">
                                    <div class="accordion-body lh-2">
                                  {!! $question->body !!}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            {{-- <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionPayment-2" aria-controls="accordionPayment-2">
                                        چگونه هزینه سفارش خود را پرداخت کنم؟
                                    </button>
                                </h2>
                                <div id="accordionPayment-2" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و
                                        برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionPayment-3" aria-controls="accordionPayment-3">
                                        اگر در ثبت سفارش با مشکل مواجه شوم چه باید بکنم؟
                                    </button>
                                </h2>
                                <div id="accordionPayment-3" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها
                                        <a href="javascript:void(0);">لورم ایپسوم متن</a> لورم ایپسوم متن ساختگی با تولید
                                        سادگی
                                        <strong>1-000-000-000</strong>لورم ایپسوم متن ساختگی
                                        <a href="javascript:void(0);">order@companymail.com</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionPayment-4" aria-controls="accordionPayment-4">
                                        برای محصول نهایی که فقط برای کاربران پولی قابل دسترسی است به کدام مجوز نیاز دارم؟
                                    </button>
                                </h2>
                                <div id="accordionPayment-4" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و
                                        برای شرایط فعلی تکنولوژی
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionPayment-5" aria-controls="accordionPayment-5">
                                        آیا اشتراک من به طور خودکار تمدید می شود؟
                                    </button>
                                </h2>
                                <div id="accordionPayment-5" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و
                                        برای شرایط فعلی تکنولوژی مورد نیاز
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    @endforeach
                    {{-- <div class="tab-pane fade" id="delivery" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div>
                                <span class="badge bg-label-primary rounded-2 p-2 mt-1">
                                    <i class="bx bx-cart fs-3 lh-1"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">
                                    <span class="align-middle">ارسال</span>
                                </h5>
                                <span class="lh-1-85">دریافت راهنمایی برای ارسال</span>
                            </div>
                        </div>
                        <div id="accordionDelivery" class="accordion accordion-header-primary">
                            <div class="card accordion-item active">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        aria-expanded="true" data-bs-target="#accordionDelivery-1"
                                        aria-controls="accordionDelivery-1">
                                        چگونه سفارش من را ارسال می کنید؟
                                    </button>
                                </h2>
                                <div id="accordionDelivery-1" class="accordion-collapse collapse show">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و
                                        برای شرایط فعلی تکنولوژی مورد نیاز
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionDelivery-2" aria-controls="accordionDelivery-2">
                                        هزینه ارسال سفارش من چقدر است؟
                                    </button>
                                </h2>
                                <div id="accordionDelivery-2" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و مجله
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionDelivery-4" aria-controls="accordionDelivery-4">
                                        اگر محصول من آسیب دیده به دستم برسد چه باید کرد؟
                                    </button>
                                </h2>
                                <div id="accordionDelivery-4" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک
                                        <a href="javascript:void(0);">تیم پشتیبانی</a>لورم ایپسوم متن ساختگی با تولید سادگی
                                        نامفهوم از صنعت چاپ و با استفاده
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="cancellation" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div>
                                <span class="badge bg-label-primary rounded-2 p-2 mt-1">
                                    <i class="bx bx-revision fs-3 lh-1"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0"><span class="align-middle">انصراف و مرجوعی</span></h5>
                                <span class="lh-1-85">دریافت راهنمایی برای انصراف و مرجوعی</span>
                            </div>
                        </div>
                        <div id="accordionCancellation" class="accordion accordion-header-primary">
                            <div class="card accordion-item active">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        aria-expanded="true" data-bs-target="#accordionCancellation-1"
                                        aria-controls="accordionCancellation-1">
                                        لورم ایپسوم متن ساختگی
                                    </button>
                                </h2>
                                <div id="accordionCancellation-1" class="accordion-collapse collapse show">
                                    <div class="accordion-body lh-2">
                                        <p>
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                            گرافیک است. چاپگرها و متون
                                        </p>
                                        <p class="mb-0">
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                            گرافیک است. چاپگرها و
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionCancellation-2" aria-controls="accordionCancellation-2">
                                        آیا می توانم محصول خود را برگردانم؟
                                    </button>
                                </h2>
                                <div id="accordionCancellation-2" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از
                                        <a href="javascript:void(0);">تیم پشتیبانی</a>لورم ایپسوم متن ساختگی با تولید سادگی
                                        نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک
                                    </div>
                                </div>
                            </div>

                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        aria-controls="accordionCancellation-3" data-bs-target="#accordionCancellation-3">
                                        از کجا می توانم وضعیت بازگشت را مشاهده کنم؟
                                    </button>
                                </h2>
                                <div id="accordionCancellation-3" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        <p>لورم ایپسوم متن ساختگی با <a href="javascript:void(0);">سفارشات</a></p>
                                        <p class="mb-0">انتخاب <strong>لورم ایپسوم متن</strong> وضعیت</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="orders" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div>
                                <span class="badge bg-label-primary rounded-2 p-2 mt-1">
                                    <i class="bx bx-box fs-3 lh-1"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">
                                    <span class="align-middle">سفارشات من</span>
                                </h5>
                                <span class="lh-1-85">جزئیات سفارش</span>
                            </div>
                        </div>
                        <div id="accordionOrders" class="accordion accordion-header-primary">
                            <div class="card accordion-item active">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        aria-expanded="true" data-bs-target="#accordionOrders-1"
                                        aria-controls="accordionOrders-1">
                                        آیا سفارش من موفقیت آمیز بوده است؟
                                    </button>
                                </h2>
                                <div id="accordionOrders-1" class="accordion-collapse collapse show">
                                    <div class="accordion-body lh-2">
                                        <p>
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                            گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و
                                            برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف
                                        </p>
                                        <p class="mb-0">
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                            گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان
                                            <strong>1-000-000-000</strong>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionOrders-2" aria-controls="accordionOrders-2">
                                        کد تبلیغاتی من کار نمی کند، چه کاری می توانم انجام دهم؟
                                    </button>
                                </h2>
                                <div id="accordionOrders-2" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده
                                        <strong>1 000 000 000</strong> لورم ایپسوم متن
                                    </div>
                                </div>
                            </div>

                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionOrders-3" aria-controls="accordionOrders-3">
                                        چگونه سفارشات خود را پیگیری کنم؟
                                    </button>
                                </h2>
                                <div id="accordionOrders-3" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        <p>
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ
                                            <a href="javascript:void(0);">لورم</a> لورم ایپسوم <strong>لورم
                                                ایپسوم</strong>.
                                        </p>
                                        <p class="mb-0">
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت
                                            <a href="javascript:void(0);">لورم</a> لورم ایپسوم متن ساختگی با تولید سادگی
                                            نامفهوم
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="product" role="tabpanel">
                        <div class="d-flex align-items-center mb-3 gap-3">
                            <div>
                                <span class="badge bg-label-primary rounded-2 p-2 mt-1">
                                    <i class="bx bx-camera fs-3 lh-1"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">
                                    <span class="align-middle">محصول و خدمات</span>
                                </h5>
                                <span class="lh-1-85">دریافت راهنمایی برای محصولات و خدمات</span>
                            </div>
                        </div>
                        <div id="accordionProduct" class="accordion accordion-header-primary">
                            <div class="card accordion-item active">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        aria-expanded="true" data-bs-target="#accordionProduct-1"
                                        aria-controls="accordionProduct-1">
                                        آیا پس از ارسال سفارش به من اطلاع داده می شود؟
                                    </button>
                                </h2>

                                <div id="accordionProduct-1" class="accordion-collapse collapse show">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه
                                    </div>
                                </div>
                            </div>

                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionProduct-2" aria-controls="accordionProduct-2">
                                        از کجا می توانم اطلاعات گارانتی را پیدا کنم؟
                                    </button>
                                </h2>
                                <div id="accordionProduct-2" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و <a href="javascript:void(0);">لورم</a>.
                                    </div>
                                </div>
                            </div>

                            <div class="card accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#accordionProduct-3" aria-controls="accordionProduct-3">
                                        چگونه می توانم پوشش گارانتی اضافی خریداری کنم؟
                                    </button>
                                </h2>
                                <div id="accordionProduct-3" class="accordion-collapse collapse">
                                    <div class="accordion-body lh-2">
                                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان
                                        گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و
                                        برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی
                                        می باشد. کتابهای
                                        <a href="javascript:void(0);">لورم</a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- /FAQ's -->
        </div>

        <!-- Contact -->
        <div class="row mt-5">
            <div class="col-12 text-center mb-4">
                <div class="badge bg-label-primary">سوالی دارید؟</div>
                <h4 class="my-2 secondary-font">همچنان سوالی دارید؟</h4>
                <p>
                    اگر سوالی را در سوالات متداول ما پیدا نمی کنید، همواره می توانید با ما تماس بگیرید. ما در اسرع وقت به
                    شما پاسخ می دهیم.
                </p>
            </div>
        </div>
        <div class="row text-center justify-content-center gap-sm-0 gap-3">
            <div class="col-sm-6">
                <div class="py-3 rounded bg-faq-section text-center">
                    <span class="badge bg-label-primary rounded-3 p-2 my-3">
                        <i class="bx bx-phone bx-sm"></i>
                    </span>
                    <h4 class="mb-2"><a class="h4" href="tel:03491010025" dir="ltr">034-91010025</a></h4>
                    <p>ما همیشه خوشحالیم که به شما کمک کنیم</p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="py-3 rounded bg-faq-section text-center">
                    <span class="badge bg-label-primary rounded-3 p-2 my-3">
                        <i class="bx bx-envelope bx-sm"></i>
                    </span>
                    <h4 class="mb-2"><a class="h4" href="mailto:help@help.com">info.helisoft@gmail.com</a></h4>
                    <p>بهترین راه برای دریافت یک پاسخ سریع</p>
                </div>
            </div>
        </div>
        <!-- /Contact -->
    </div>
    <!--/ Content -->
@stop

@section('footer-scripts')
@stop
