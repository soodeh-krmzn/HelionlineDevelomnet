<?php

namespace App\Models;

use App\Models\MyModels\Main;
use App\Models\Price;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;
use App\Traits\Syncable;

class GameMeta extends Main
{
    use HasFactory, SoftDeletes;
    use Syncable;

    protected $guarded = [];

    public static $normal_price;
    public static $vip_price;
    public static $totalPrice;


    public function changeKey()
    {
        switch ($this->key) {
            case "normal":
                echo "vip";
                break;
            case "vip":
                echo "normal";
                break;
            default:
                echo $this->key;
        }
    }

    public function section()
    {
        return $this->belongsToMany(Section::class, "games", "id", "section_id", "g_id");
    }

    public function showIndex($g_id, $edit = true)
    {
        return $this->showIndex2($g_id);
    }

    public function showIndex2($g_id, $edit = true)
    {
        $GLOBALS['vip_price'] = 0;
        $GLOBALS['normal_price'] = 0;
        $GLOBALS['total_price'] = 0;
        $GLOBALS['vip_min'] = 0;
        $GLOBALS['normal_min'] = 0;
        $GLOBALS['entrances'] = 0;
        $metas = GameMeta::where("g_id", $g_id)->get();
        if ($metas->count() > 0) {
            return view('game.loadMeta', compact('metas', 'edit'))->render();
        }
    }
    public function showIndexLog($g_id)
    {
        $metas = GameMeta::where("g_id", $g_id)->get();
        if ($metas->count() > 0) {
            return view('game.loadMetaLog', compact('metas'))->render();
        }
    }

    public function crudGame($g_id)
    {
        $meta = new GameMeta;
?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo __('تغییرات'); ?></h3>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 form-group" id="offer-code-field">
                    <label class="form-label required"><?php echo __('تعداد'); ?> <span class="text-danger">*</span></label>
                    <input type="number" name="meta-count" id="meta-count" class="form-control" min="1" max="200">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required"><?php echo __('نوع'); ?> <span class="text-danger">*</span></label>
                    <select name="meta-type" id="meta-type" class="form-select">
                        <option value="normal"><?php echo __('عادی'); ?></option>
                        <option value="vip"><?php echo __('ویژه'); ?></option>
                    </select>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <button type="button" id="store-changes" data-g_id="<?php echo $g_id ?>" class="btn btn-success me-sm-3 me-1"><?php echo __('ثبت اطلاعات'); ?></button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"><?php echo __('انصراف'); ?></button>
                </div>
            </div>
            <div id="meta-result"><?= $meta->showIndex($g_id) ?></div>
        </div>
<?php
    }

    public function calcPrice($minutes = null, $pastMinutes = 0, $isolateMode = false)
    {
        // dump('test');
        // dump('110');

        if ($minutes == null) {
            $time_start = new DateTime($this->start);
            $time_end = new DateTime($this->end);
            $interval = $time_start->diff($time_end);
            $minutes = $interval->days * 24 * 60;
            $minutes += $interval->h * 60;
            $minutes += $interval->i;
        }
        $priceMinutelevel = $pastMinutes + $minutes;

        $section = $this->section->first();

        if ($section->type == 'stair') {
            $result = $this->stairType($section, $priceMinutelevel, $minutes);
        } else {
            $result = $this->waterfallType($section, $priceMinutelevel, $minutes, $isolateMode);
        }
        if (isset($result['message'])) {
            return $result;
        }

        return [
            'price' => $result['price'],
            'section_id' => $section->id,
            'ratePrice' => $result['ratePrice'],
            'minutes' => $minutes,
            'entrance' => $result['entrance'],
            'calc_type' => $result['calc_type'],
            'isStair' => $section->type == 'stair'
        ];
    }

    public function stairType($section, $totalMinutes, $minutes)
    {
        $prs = Price::where('section_id', $section->id)
            ->where("price_type", $this->key)
            ->where("from", "<=", $totalMinutes)->orderBy('from')->get();
        if (count($prs) == 0) {
            return [
                'message' => __('messages.price_error', ['minutes' => $minutes, 'section_name' => $section->name, 'calc_type' => __($this->key)]),
                'section_id' => $section->id
            ];
        }
        $price = 0;
        $entrance = 0;

        $nop = count($prs); //=== numberOfPrices
        $prsPastMinutes = 0;
        $from=0;
        foreach ($prs as $key => $pr) {
            //check price bond
            if (!in_array($pr->from,[$from,$from+1])){
                return [
                    'message' => __('messages.price_error', ['minutes' => $from, 'section_name' => $section->name, 'calc_type' => __($this->key)]),
                    'section_id' => $section->id
                ];
            }
            $from=$pr->to;
            //end
            if ($key == $nop - 1) {
                if ($pr->calc_type == 'min') {
                    $price += $pr->price * ($totalMinutes - $prsPastMinutes)*$this->value;
                } else {
                    $price += $pr->price*$this->value;
                }
            } else {
                $minuteRange = $pr->to - $pr->from;
                $prsPastMinutes += $minuteRange;
                if ($pr->calc_type == 'min') {
                    $price += $pr->price * $minuteRange*$this->value;
                } else {
                    $price += $pr->price*$this->value;
                }
            }
            $entrance += $pr->entrance_price;

        }

        return ['price' => $price, 'entrance' => $entrance, 'ratePrice' => null, 'calc_type' => $pr->calc_type];
    }
    public function waterfallType($section, $priceMinutelevel, $minutes, $isolateMode)
    {
        $pr = Price::where('section_id', $section->id)
            ->where("price_type", $this->key)
            ->where("from", "<=", $priceMinutelevel)
            ->where("to", ">=", $priceMinutelevel)->first();
        if ($pr == null) {
            return [
                'message' => __('messages.price_error', ['minutes' => $minutes, 'section_name' => $section->name, 'calc_type' => __($this->key)]),
                'section_id' => $section->id
            ];
        }
        if ($pr->calc_type == "min") {
            // dump(($isolateMode ? $minutes : $priceMinutelevel),$minutes,$priceMinutelevel);
            $price = $pr->price * ($isolateMode ? $minutes : $priceMinutelevel);
            // dump($pr->price);
            $price *= $this->value;
        } else if ($pr->calc_type == "all") {
            $price = $pr->price;
            $price *= $this->value;
        }
        $entrance = $pr->entrance_price;
        return ['price' => $price, 'entrance' => $entrance, 'ratePrice' => $pr->price, 'calc_type' => $pr->calc_type];
    }
}
