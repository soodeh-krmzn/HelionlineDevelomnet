<?php

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\PersonMeta;
use App\Services\Database;
use Hekmatinasser\Verta\Verta;

function makeRound($price)
{
    $setting = new Setting();
    $status = $setting->getSetting('round-status');
    if ($status) {
        $type = $setting->getSetting('round-type');
        $odd = $setting->getSetting('round-odd');
        $number = $price / $odd;
        $parts = explode(".", (string)$number);

        if (empty($parts[1])) {
            return $price;
        }
        $decimal_part = $parts[1];
        if ($decimal_part > 0) {
            return $type == "top" ? (intval($number) + 1) * $odd : intval($number) * $odd;
        } else {
            return intval($number) * $odd;
        }
    }
    return $price;
}

function curr()
{
    $setting = new Setting();
    $curr = $setting->getSetting('curr');
    return '<span class="curr-size" style="font-size:13px">' . " " . $curr . '</span>';
}

function minLabel($type = null)
{
    return '<span style="font-size:13px"> ' . ($type == 'times' ? __('مرتبه') : __('دقیقه')) . '</span>';
}

function breadcrumb()
{
    $url = getUrl();
    $menu = \App\Models\Admin\Menu::where("url", $url)->first();
    if ($menu) {
        $id = $menu->id;
        $parent = $menu->parent?->label;
        $label = $menu->name;
        $icon = $menu->icon;
        $url = $menu->url;
        $learn_url = $menu->learn_url;
        $details = $menu->details;
    } else {
        $id = "";
        $parent = "";
        $label = __("ناشناس");
        $icon = "";
        $url = "";
        $learn_url = "";
        $details = "";
    }
?>
    <h4 class="py-3 breadcrumb-wrapper">
        <button id="toggle-help" class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#help-offcanvas" aria-controls="help-offcanvas">
            <i class="bx bx-question-mark"></i>
        </button>
        <span class="text-muted fw-light"><?php echo $parent; ?> / </span> <?php echo $label; ?>
    </h4>
    <?php
    if ($details) {
    ?>
        <p class="mb-4">
            &nbsp
            <?php echo $details; ?>
        </p>
    <?php
    }
    ?>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="help-offcanvas" aria-labelledby="help-offcanvas">
    </div>
<?php
}

function getIp()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return request()->ip(); // it will return the server IP if the client IP is not found using this method.
}

function active_menu($route)
{
    if (is_array($route))
        return in_array(getUrl(), $route) ? ' active' : '';
    else
        return getUrl() == $route ? ' active' : '';
}

function getUrl()
{
    $url = request()->route()->uri;
    $parameters = request()->route()->parameters;
    foreach ($parameters as $key => $value) {
        $url = str_replace("{" . $key . "}", $value, $url);
    }
    return $url;
}

function remove_($string)
{
    if (app()->getLocale() == 'fa' and $string == 'wallet') {
        return "کیف پول";
    }
    return str_replace(['_', '-'], ' ', $string);
}

function getMonth()
{
    if (app()->getLocale() == 'fa') {
        return verta(now())->month;
    } else {
        return now()->month;
    }
}

function getDay()
{
    if (app()->getLocale() == 'fa') {
        return verta(now())->day;
    } else {
        return now()->day;
    }
}

function persianTime($date)
{
    if ($date) {
        return verta($date)->format('Y/m/d H:i');
    }
}

function formatDuration($minutes)
{
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    $duration = sprintf("%d:%d", $hours, $remainingMinutes);
    return $duration;
}
function formatDurationStr($minutes)
{
    if ($minutes == 0) {
        return 0;
    }
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;

    $str = $hours ? $hours . ' ' . __('ساعت') : '';
    $str .= ($remainingMinutes and $hours != 0) ?' ' . __('و').' ' : '';
    $str.=$remainingMinutes ? $remainingMinutes.' '. __('دقیقه') : '';
    return  $str;
}

function timeFormat($date, $instance = false)
{
    if ($date) {
        // dd(app()->getLocale());
        if (app()->getLocale() == 'fa') {
            if ($instance) {
                return verta($date);
            }
            if ($date) {
                return verta($date)->format('H:i Y/m/d');
            }
        } else {
            if ($instance) {
                return Carbon::create($date);
            }
            if ($date) {
                return Carbon::create($date)->format('Y/m/d H:i');
            }
        }
    }
}

function currentDateTime()
{
    if (app()->getLocale() == 'fa') {
        return verta()->format('Y/m/d H:i:s');
    } else {
        return now()->format('Y/m/d H:i:s');
    }
}
function CurrentTime(){
    if (app()->getLocale() == 'fa') {
        return verta()->format('H:i');
    } else {
        return now()->format('H:i');
    }
}
function dateSetFormat($date, $to = 0, $instance = 0)
{
    if ($date) {
        // dd(app()->getLocale());
        if (app()->getLocale() == 'fa') {
            if ($instance) {
                return Verta::parse($date)->toCarbon();
            }
            if ($to) {
                return Verta::parse($date)->endDay()->formatGregorian('Y-m-d H:i:s');
            }
            return Verta::parse($date)->formatGregorian('Y-m-d');
        }
        if ($instance) {
            return Carbon::parse($date);
        }
        if ($to) {
            // dd($to);
            return Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s');
        }
        return Carbon::parse($date)->format('Y-m-d');
    }
}

function dateTimeSetFormat($date)
{
    if ($date) {
        // dd(app()->getLocale());
        if (app()->getLocale() == 'fa') {
            if ($date) {
                return Verta::parse($date)->formatGregorian('Y-m-d H:i:s');
            }
        }
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }
}

function dateFormat($date)
{
    if ($date) {
        // dd(app()->getLocale());
        if (app()->getLocale() == 'fa') {
            if ($date) {
                return verta($date)->format('Y/m/d');
            }
        } else {
            if ($date) {
                return Carbon::create($date)->format('Y/m/d');
            }
        }
    }
}

function persianJustDate($date)
{
    if ($date) {
        return verta($date)->format('Y/m/d');
    }
}

function gregorianTime($date)
{
    if ($date) {
        return Carbon::create($date)->format('Y/m/d H:i');
    }
}

function gregorianJustDate($date)
{
    if ($date) {
        return Carbon::create($date)->format('Y/m/d');
    }
}

function convertToPersian($string)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];

    $num = range(0, 9);
    $convertedPersianNums = str_replace($num, $persian, $string);
    $englishNumbersOnly = str_replace($num, $arabic, $convertedPersianNums);

    return $englishNumbersOnly;
}

function price($amount)
{
    if ($amount) {
        return cnf($amount);
    }
}

function dateToLangExcel($date)
{
    return app()->getLocale() == 'fa' ? persianTime($date) : gregorianTime($date);
}

function ert($variable)
{
    if ($variable == 't-a') { //ticket-attachment
        return 'uploads/thickets/';
    }
    if ($variable == 'cd') {  //confirmDelete
        confirmDelete('مطمئنید؟', 'آیا از حذف این مورد اطمینان دارید؟');
        return true;
    }
    dd('ورودی اشتباهه');
}

function doUpload($file, $path)
{
    $fileName = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path($path), $fileName);
    return $fileName;
}

function getPersonMeta($personId, $key)
{
    $meta = PersonMeta::where('p_id', $personId)->where('meta', $key)->first();
    return $meta->value ?? "";
}

function cnf($number, $decimals = 2, $decimal_separator = '.', $thousands_separator = ',')
{
    $formattedNumber = number_format($number, $decimals, $decimal_separator, $thousands_separator);
    if ($decimals > 0) {
        $formattedNumber = rtrim(rtrim($formattedNumber, '0'), $decimal_separator);
    }
    return $formattedNumber;
}
