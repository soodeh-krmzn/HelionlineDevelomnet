<div class="">
    <table class="table table-hover table-bordered">
        <tbody>
            @php
                $pastMinutes = 0;
            @endphp
            @foreach ($metas->groupBy('u_id') as $u_id => $u_idMetas)
                @php
                    $u_idMinutes = 0;
                @endphp
                @foreach ($u_idMetas as $meta)
                    @php
                        $time_start = new DateTime($meta->start);
                        $time_end = new DateTime($meta->end);
                        $interval = $time_start->diff($time_end)->format('%h:%i');
                        $metaInfo = $meta->calcPrice(null, $u_idMinutes);
                        $pastMinutes += $metaInfo['minutes'] ?? 0;
                        $u_idMinutes += $metaInfo['minutes'] ?? 0;
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
                                        @if ($metaInfo['ratePrice'] ?? false)
                                            <th class="text-center">@lang('نرخ')</th>
                                        @endif
                                        <?php if ($metaInfo['message'] ?? false or ($metaInfo['calc_type'] ?? '') == 'min') { ?>
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
                                            <span class="badge bg-<?php echo $meta->key == 'normal' ? 'danger' : 'warning'; ?>"><?= __($meta->key) ?></span>
                                        </td>


                                        @if ($metaInfo['ratePrice'] ?? false)
                                            <td class="text-center"><?php echo cnf($metaInfo['ratePrice']) . curr(); ?></td>
                                        @endif
                                        <?php
                                        if ($metaInfo['message'] ?? false or ($metaInfo['calc_type'] ?? '') == 'min') { ?>
                                        <td><?php echo $metaInfo['message'] ?? cnf($metaInfo['ratePrice'] * $metaInfo['minutes'] * $meta->value) . curr();
                                        ?></td>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <?php
                                    if ($edit) {
                                    ?>
                        <td class="text-center no-print">
                            <?php
                                            if ($meta->calcPrice()['calc_type']??'' == 'min') {
                                                if (is_null($meta->end)) {
                                            ?>
                            <button data-id="<?php echo $meta->id; ?>" class="btn btn-warning btn-sm update-changes"
                                data-action="pause">
                                <i data-toggle="tooltip" title="@lang('توقف')" class="bx bx-pause"></i>
                            </button>
                            <?php
                                                } elseif ($meta->close == 0) {
                                                ?>
                            <button data-id="<?php echo $meta->id; ?>"
                                class="btn btn-sm <?= $meta->close ? 'btn-info' : 'update-changes btn-success' ?>"
                                data-action="play">
                                <i data-toggle="tooltip" title="@lang('شروع')" class="bx bx-play"></i>
                            </button>
                            <?php
                                                }
                                            } else {  ?>
                            <button class="btn btn-info btn-sm price_type_error">
                                <i data-toggle="tooltip" title="@lang('توقف')" class="bx bx-pause"></i>
                            </button>
                            <?php
                                            }
                                            if (Gate::allows('delete')) {
                                            ?>
                            <button class="btn btn-danger btn-sm delete-changes" data-g_id="<?= $meta->g_id ?>"
                                data-id="<?php echo $meta->id; ?>">
                                <i data-toggle="tooltip" title="@lang('حذف')" class="bx bxs-trash"></i>
                            </button>
                            <?php
                                            }
                                            ?>
                            <button type="button" class="btn btn-secondary btn-sm edit-meta" data-id="<?php echo $meta->id; ?>">
                                <i data-toggle="tooltip" title="@lang('ویرایش')" class="bx bxs-pencil"></i>
                            </button>
                        </td>
                        <?php
                                    }
                                    ?>
                    </tr>
                    @php
                        $meta->update([
                            'rate_price' => $metaInfo['ratePrice'] ?? 0,
                            'rate_type' => $metaInfo['calc_type'] ?? null,
                        ]);
                    @endphp
                @endforeach
                @php
                    $coursePrice = $meta->calcPrice($u_idMinutes)['price'] ?? 0;
                    $GLOBALS['total_price'] += $coursePrice;
                @endphp
                <tr class="mb-3 no-print" style="border-bottom: 19px solid #3879304a;">
                    <td><strong>@lang('مدت') : <?= formatDuration($u_idMinutes *= $meta->value) ?></strong></td>
                    <td class="text-center"><strong> @lang('جمع'): <?= cnf($coursePrice) . curr() ?></strong></td>
                </tr>
                @php
                    $entrances = 0;
                    if ($meta->key == 'vip') {
                        $GLOBALS['vip_price'] += $coursePrice;
                        $GLOBALS['vip_min'] += $u_idMinutes;
                        $prForEntrance = App\Models\Price::where('section_id', $metaInfo['section_id'])
                            ->where('price_type', 'vip')
                            ->where('from', '<=', $u_idMinutes)
                            ->get();
                        foreach ($prForEntrance as $price) {
                            $entrances += $price->entrance_price;
                        }
                    } elseif ($meta->key == 'normal') {
                        $GLOBALS['normal_price'] += $coursePrice;
                        $GLOBALS['normal_min'] += $u_idMinutes;
                    }
                    $entrances = ($metaInfo['entrance'] ?? 0) * $meta->value;
                    // dump($entrances);
                    $GLOBALS['entrances'] += $entrances;
                    // dump($GLOBALS['entrances']);
                @endphp
            @endforeach
        </tbody>
    </table>
</div>
