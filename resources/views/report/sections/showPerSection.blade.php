<div class="table-responsive">
    <table style="border-radius: 15px" class="table overflow-hidden table-info table-bordered table-striped">
        <thead>
            <tr>
                <th>@lang('نام بخش')</th>
                <th class="text-center">@lang('تعداد')<br> @lang('(مشتریان | صورتحساب)')</th>
                <th>@lang('مجموع بخش')</th>
                <th>@lang('مجموع فروشگاه')</th>
                <th>@lang('مجموع تخفیف')</th>
                <th>@lang('مجموع مالیات')</th>
                <th>@lang('مجموع رندشده')</th>
                <th>@lang('مجموع نهایی')</th>
            </tr>
            @php
                $total = collect([]);
                $total->game_price = 0;
                $total->offer_price = 0;
                $total->vat_price = 0;
                $total->round_price = 0;
                $total->final_price = 0;
                $total->total_shop = 0;
                $total->count_games = 0;
                $total->sum_count = 0;
            @endphp
            @foreach ($data as $section)
                <tr>
                    <th>{{ $section->section_name }}</th>
                    <td class="text-center" style="direction: ltr">{{ $section->count_games }} | {{ $section->sum_count }}
                    </td>
                    <td>{{ cnf($section->game_price) }}</td>
                    <td>{{ cnf($section->total_shop) }}</td>
                    <td>{{ cnf($section->offer_price) }}</td>
                    <td>{{ cnf($section->vat_price) }}</td>
                    <td style="direction: ltr">{{ cnf($section->round_price) }}</td>
                    <td>{{ cnf($section->final_price) }}</td>
                </tr>
                @php
                    $total->game_price += $section->game_price;
                    $total->offer_price += $section->offer_price;
                    $total->vat_price += $section->vat_price;
                    $total->round_price += $section->round_price;
                    $total->final_price += $section->final_price;
                    $total->total_shop += $section->total_shop;
                    $total->count_games += $section->count_games;
                    $total->sum_count += $section->sum_count;
                @endphp
            @endforeach
            <tr class="table-warning">
                <th colspan="3">@lang('مجموع فروشگاه (آزاد)')</th>
                <td colspan="1">{{ cnf($factors['total_price']) }}</td>
                <td colspan="1">{{ cnf($factors['offer_price']) }}</td>
                <td colspan="2"></td>
                <td colspan="1">{{ cnf($factors['final_price']) }}</td>

            </tr>
            <tr class="table-warning">
                <th colspan="3">@lang('فروش بسته')</th>
                <td colspan="4"></td>
                <td>{{ cnf(abs($charges)) }}</td>
            </tr>
            <tr class="table-warning">
                <th colspan="3">@lang('کلاس ها')</th>
                <td colspan="4"></td>
                <td>{{ cnf(abs($courses)) }}</td>
                @php
                    $total->total_shop += $factors['total_price'];
                    $total->offer_price += $factors['offer_price'];
                    $total->final_price += $factors['final_price'] + abs($charges) + abs($courses);
                    // $total->final_price += $factors['final_price'] + abs($courses);
                @endphp
            </tr>
            <tr class="table-primary" style="border-top: 2px solid green">
                <th>@lang('مجموع')</th>
                <td class="text-center" style="direction: ltr">{{ $total->count_games }} | {{ $total->sum_count }}
                </td>
                <td>{{ cnf($total->game_price) }}</td>
                <td>{{ cnf($total->total_shop) }}</td>
                <td>{{ cnf($total->offer_price) }}</td>
                <td>{{ cnf($total->vat_price) }}</td>
                <td style="direction: ltr">{{ cnf($total->round_price) }}</td>
                <td class="">{{ cnf($total->final_price) }}</td>
            </tr>

        </thead>
        <tbody>

        </tbody>
    </table>
</div>
