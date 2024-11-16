<table style="border-radius: 15px" class="table overflow-hidden table-info table-bordered table-striped">
    <thead>
        <tr style="border-bottom: 2px solid green">
            <th class="text-center" colspan='20'>@lang('پرداخت ها')</th>
        </tr>
        @php
            $total = 0;
        @endphp
        @foreach ($payments as $payment)
            <tr>
                <th style="width: 50%">{{remove_( $payment->type) }}</th>
                <td>{{ cnf($payment->price) }}</td>
            </tr>
            @php
                $total+=$payment->price;
            @endphp
        @endforeach
        <tr style="border-top: 2px solid green">
            <th>@lang('مجموع')</th>
            <td>{{cnf($total)}}</td>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
