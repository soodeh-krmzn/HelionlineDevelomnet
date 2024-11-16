<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    {{-- <link rel="font" href="{{asset('assets/vendor/fonts/farsi-fonts-styles-fa-num/secondary-estedad.css')}}"> --}}
    <style>
        @if (app()->getLocale() == 'fa')
            @import url("{{ asset('assets/vendor/fonts/farsi-fonts-styles-fa-num/secondary-estedad.css') }}");

            table {
                direction: rtl;
            }
        @endif

        body * {
            font-family: "secondary-font", "segoe ui", "tahoma" !important;
        }

        table {
            width: 100%;
            text-align: center;
        }

        table td,
        table th {
            border: 4px solid #000;
        }


        @media print {
            body{
                width: fit-content !important;
            }
            th {
                font-size: {{ $sizes['title'] }};
            }

            td {
                font-size: {{ $sizes['text'] }};
            }

            .head-size {
                font-size: {{ $sizes['head'] }};
            }

            .curr-size {
                font-size: {{ $sizes['curr'] }}
            }

            @page {
                margin: 2px;
            }

            div,
            table * {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div style="text-align: center">
        {!! $bill['head'] !!}
    </div>
    <table class="table table-bordered text-center w-100" style=" table-layout: auto;">
        <tbody>
            <tr>
                <th class="text-center head-size">
                    <b>@lang('مشتری'): </b> <br> {{ $game->person_fullname }}
                </th>
                <th class="text-center head-size">
                    <b>@lang('موبایل'): </b> <br> {{ $game->person_mobile }}
                </th>
                <th class="text-center head-size">
                    <b>@lang('تاریخ'): </b> <br>
                    {{ timeFormat($game->out_date . ' ' . $game->out_time) }}
                </th>
                <th class="text-center head-size">
                    <b>@lang('بخش'): </b> <br> {{ $game->section_name }}
                </th>
                <th class="text-center head-size">
                    <b>@lang('کد'): </b> <br> {{ $game->id }}
                </th>
            </tr>
        </tbody>
        <div class="text-center w-100">

    </table>
    <table class="bill-table table-bordered table text-center mt-1 mb-1">
        <tbody>
            @foreach ($game->metas->groupBy('u_id') as $u_id => $u_idMetas )

            <tr class="bg-light">
                <th class="text-center">@lang('شروع و پایان')</th>
                <th class="text-center">@lang('مدت')</th>
                <th class="text-center">@lang('تعداد')</th>
                <th class="no-print text-center">@lang('نوع')</th>
                <th class="text-center">@lang('نرخ')</th>

                <th class="text-center">@lang('مبلغ')</th>

            </tr>
            @foreach ($u_idMetas as $meta)
                @php
                    $time_start = new DateTime($meta->start);
                    $time_end = new DateTime($meta->end);
                    $interval = $time_start->diff($time_end)->format('%h:%i');
                    $interval2 = $time_start->diff($time_end);
                    $minutes = $interval2->days * 24 * 60;
                    $minutes += $interval2->h * 60;
                    $minutes += $interval2->i;
                @endphp
                <tr>
                    <td class="text-center">
                        از
                        {{ timeFormat($meta->start, 1)->format('H:i') }}
                        @if ($meta->end)
                        تا
                        {{ timeFormat($meta->end, 1)->format('H:i') }}
                        @endif
                    </td>
                    <td class="text-center">{{ $interval }}</td>
                    <td class="text-center">{{ $meta->value }}</td>
                    <td class="no-print text-center">
                        <span class="badge bg-danger">{{ $meta->key=='normal' ? __('عادی') : __('ویژه') }}</span>
                    </td>


                    <td class="text-center">{!! cnf($meta->rate_price) . curr() !!}</td>
                    <td>{!! $meta->rate_type=='all'?'-':cnf($meta->rate_price * $meta->value * $minutes) . curr() !!}</td>
                </tr>
            @endforeach
            @endforeach

        </tbody>
    </table>
    <table class="table table-bordered table-success checkout-table text-center mb-1">
        <tbody>
            <tr>
                <th>
                    @lang('جمع عادی')
                </th>
                <td>
                    {!! $game->total . minLabel() !!}
                </td>
                <th>
                    @lang('مبلغ عادی')
                </th>
                <td>
                    {!! cnf($game->total_price) . curr() !!}
                </td>
            </tr>
            <tr class="table-success">
                <th>
                    @lang('جمع ویژه')
                </th>
                <td>
                    {!! $game->total_vip . minLabel() !!}
                </td>
                <th>
                    @lang('مبلغ ویژه')
                </th>
                <td>
                    {!! cnf($game->total_vip_price) . curr() !!}
                </td>
            </tr>
            @if ($game->extra > 0)
                <tr>
                    <th>
                        @lang('جمع مازاد')
                    </th>
                    <td>
                        {!! $game->extra . minLabel() !!}
                    </td>
                    <th>
                        @lang('مبلغ مازاد')
                    </th>
                    <td>
                        {!! cnf($game->extra_price) . curr() !!}
                    </td>
                </tr>
            @endif
            <tr class="table-success">
                <th colspan="2">
                    @lang('مبلغ ورودی')
                </th>
                <td colspan="2">
                    {!! cnf($game->login_price) . curr() !!}
                </td>
            </tr>
            <tr class="border-bottom-2">
                <th colspan="100%">
                    @lang('مجموع بخش:') {!! cnf($game->game_price) . curr() !!}
                </th>
            </tr>
        </tbody>
    </table>
    @if ($game->used_sharj > 0)
        <table class="table table-bordered table-primary checkout-table text-center m-0">
            <tbody>
                <tr class="bg-warning">
                    <th>
                        @lang('شارژ استفاده شده')
                    </th>
                    <td>
                        {!! $game->used_sharj . minLabel($game->sharj_type) !!}
                    </td>
                    <th>
                        @lang('شارژ باقی مانده')
                    </th>
                    <td>
                        {!! $game->initial_sharj - $game->used_sharj . minLabel($game->sharj_type) !!}
                    </td>
                </tr>
                <tr class="bg-warning">
                    <th colspan="2">
                        @lang('بسته در حال استفاده')
                    </th>
                    <td colspan="2">
                        {{ $game->sharj_package }}
                        <small> (@lang('انقضا') {{ dateFormat($game->person->expire) }})</small>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
    @if ($bodies = $game->factor?->bodies)
        <table class="table shop-table table-bordered table-hover">
            <thead class="table-warning">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">@lang('محصول')</th>
                    <th class="text-center">@lang('قیمت واحد')</th>
                    <th class="text-center">@lang('تعداد')</th>
                    <th class="text-center">@lang('کل')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bodies as $i => $body)
                    <tr>
                        <td class="text-center">{{ ++$i }}</td>
                        <td class="text-center">{{ $body->product_name }}</td>
                        <td class="text-center">
                            <span class="just-print">{!! cnf($body->product_price) . curr() !!}</span>
                        </td>
                        <td class="text-center">
                            <span class="just-print">{{ $body->count }}</span>
                        </td>
                        <td class="text-center">{!! cnf($body->body_price) . curr() !!}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-center" colspan="100%">
                        @lang('مجموع فروشگاه'): {!! cnf($game->total_shop) . curr() !!}
                    </th>
                </tr>
            </tbody>
        </table>
    @endif
    <table class="table table-bordered checkout-table table-info">
        <tbody>
            <tr>
                <th class="text-center">
                    @lang('تخفیف')
                    @if ($game->offer_name)
                        <span>{{ $game->offer_name }}</span>
                    @endif
                </th>
                <td class="text-center">
                    {!! cnf($game->offer_price) . curr() !!}
                </td>
                <th class="text-center">
                    @lang('جمع کل')
                </th>
                <td class="text-center">
                    @if ($game->final_price_before_round != $game->final_price_after_round)
                        <small>(<del>{{ cnf($game->final_price_before_round) }}</del>)</small>
                    @endif
                    {!! cnf($game->final_price_after_round) . curr() !!}
                </td>
            </tr>
            @if ($game->vat_rate)
                <tr>
                    <th class="text-center">
                        @lang('مالیات') <small>(%{{ $game->vat_rate }})
                    </th>
                    <td class="text-center">
                        {!! cnf($game->vat_price) . curr() !!}
                    </td>
                    <th class="text-center">
                        @lang('مبلغ نهایی')
                    </th>
                    <td class="text-center">
                        {!! cnf($game->final_price) . curr() !!}
                    </td>
                </tr>
            @endif
            <tr>
                <th class="text-center">
                    @lang('پیش پرداخت')
                </th>
                <td class="text-center">
                    {!! cnf($game->deposit) . curr() !!}
                </td>
                <th class="text-center">
                    @lang('قابل پرداخت')
                </th>
                <td class="text-center">
                    {!! cnf($game->final_price - $game->deposit) . curr() !!}
                </td>
            </tr>
        </tbody>
    </table>
    <div style="text-align: center">
        {!! $bill['sign'] !!}
    </div>
    </div>
    <script>
        window.print();
        setTimeout(() => {
            window.close();
        }, 2000);
    </script>
</body>

</html>
