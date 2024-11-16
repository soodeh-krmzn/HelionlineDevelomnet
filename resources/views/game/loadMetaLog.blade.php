@php
use Carbon\Carbon;
@endphp
<div class="">
    <table class="table table-hover table-bordered">
        <tbody>

            @foreach ($metas->groupBy('u_id') as $u_id => $u_idMetas)
            @php
                $u_idMinutes=0;
            @endphp
                @foreach ($u_idMetas as $meta)
                    @php
                        $time_start = new DateTime($meta->start);
                        $time_end = new DateTime($meta->end);
                        $interval = $time_start->diff($time_end)->format('%h:%i');
                        $minutesDiff=Carbon::create($meta->start)->diffInMinutes(Carbon::create($meta->end))
                    @endphp
                    <tr>
                        <td class="text-center p-0" colspan="1">
                            <table class="bill-table table-bordered table text-center mt-1 mb-1">
                                <tbody>
                                    <tr class='bg-light'>
                                        <th class="text-center">@lang('شروع و پایان')</th>
                                        <th class="text-center">@lang('مدت')</th>
                                        <th class="text-center">@lang('تعداد')</th>
                                        <th class="no-print text-center">@lang('نوع')</th>
                                        <th class="text-center">@lang('نرخ')</th>
                                        <?php if ($meta->rate_type == 'min') { ?>
                                        <th class="text-center">@lang('مبلغ')</th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            @lang('از') <?php echo Verta($meta->start)->format('H:i');
                                            echo $meta->end ? ' ' . __('تا') . ' ' . Verta($meta->end)->format('H:i') : ''; ?>
                                        </td>
                                        <td class="text-center"><?php echo $interval; ?></td>
                                        <td class="text-center"><?php echo $meta->value; ?></td>
                                        <td class="no-print text-center">
                                            <span class="badge bg-<?php echo $meta->key == 'normal' ? 'danger' : 'warning'; ?>"><?php echo $meta->key == 'normal' ? __('عادی') : __('ویژه'); ?></span>
                                        </td>
                                            <td class="text-center"><?php echo cnf($meta->rate_price) . curr(); ?></td>

                                        <?php
                                        if ($meta->rate_type == 'min') { ?>
                                        <td><?= cnf($meta->rate_price *  $minutesDiff * $meta->value) . curr();
                                        ?></td>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @php

                        $u_idMinutes+=$minutesDiff;
                        if ($meta->rate_type=='min') {
                            $coursePrice=$u_idMinutes*$meta->value*$meta->rate_price;
                        }else {
                            $coursePrice=$meta->value*$meta->rate_price;
                        }
                    @endphp
                @endforeach
                <tr class="mb-3 no-print" style="border-bottom: 19px solid #3879304a;">
                    <td class="d-flex justify-content-between">
                        <strong>@lang('مدت') : <?= formatDuration($u_idMinutes *= $meta->value) ?></strong>
                        <div>-</div>
                        <strong> @lang('جمع'): <?= cnf($coursePrice) . curr() ?></strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
