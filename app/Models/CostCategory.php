<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use App\Models\Cost;
use App\Models\MyModels\Main;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Gate;

class CostCategory extends Main
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'parent_id', 'details', 'code', 'display_order'];
    protected $tabel = "cost_categories";
    public function parent()
    {
        return $this->belongsTo(CostCategory::class, "parent_id");
    }

    public function costs()
    {
        return $this->belongsToMany(Cost::class, 'cost_category', 'category_id', 'cost_id');
    }

    public static function costTableReport($request)
    {
        $from_date = false;
        $to_date = false;
        if ($request['from_date'])
            $from_date = Verta::parse($request['from_date'])->toCarbon();
        if ($request['to_date'])
            $to_date = Verta::parse($request['to_date'])->toCarbon()->endOfDay();
        $categories = CostCategory::with(['costs' => function ($query) use ($from_date, $to_date) {
            if ($from_date)
                $query->where('costs.created_at', '>', $from_date);
            if ($to_date)
                $query->where('costs.created_at', '<', $to_date);
        }])->get();
        $data = array();
        foreach ($categories as $key => $category) {
            $data[$key]['name'] = $category->name;
            $data[$key]['total_price'] = $category->costs->sum('price');
        }
        return view('report.sections.costTable', compact('data'));
    }

    public function showIndex()
    {
        $costCategories = CostCategory::orderBy('created_at', 'desc')->get();
        if ($costCategories->count() > 0) {
?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th><?=__('نام دسته')?></th>
                        <th><?=__('دسته والد')?></th>
                        <th><?=__('ترتیب نمایش')?></th>
                        <th><?=__('کد')?></th>
                        <th><?=__('توضیحات')?></th>
                        <th><?=__('مدیریت')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($costCategories as $costCategory) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $costCategory->name; ?></td>
                            <td><?php echo $costCategory->parent?->name ?? __("ندارد"); ?></td>
                            <td><?php echo $costCategory->display_order; ?></td>
                            <td><?php echo $costCategory->code; ?></td>
                            <td><?php echo $costCategory->details; ?></td>
                            <td>
                                <?php
                                if (Gate::allows('update')) { ?>
                                    <a href="#category-form" class="btn btn-warning btn-sm update-cost-category" data-action="update" data-id="<?php echo $costCategory->id ?>"
                                        data-name="<?php echo $costCategory->name ?>" data-parent_id="<?php echo $costCategory->parent_id ?>" data-status="<?php echo $costCategory->status ?>"
                                        data-order="<?php echo $costCategory->display_order ?>" data-details="<?php echo $costCategory->details ?>">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                <?php
                                }
                                if (Gate::allows('delete')) { ?>
                                    <button type="button" class="btn btn-danger btn-sm delete-cost-category" data-id="<?php echo $costCategory->id ?>"><i class="bx bx-trash"></i></button>
                                <?php
                                } ?>
                            </td>
                        </tr>
                    <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
    }

    public function crud()
    {
        $costCategories = CostCategory::all();
        $cat = new CostCategory;
        $title = __("دسته بندی هزینه");
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <form id="category-form">
                <div class="text-center mb-4 mt-0 mt-md-n2">
                    <h3 class="secondary-font"><?php echo $title; ?></h3>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('نام')?> <span class="text-danger">*</span></label>
                        <input type="text" name="c-name" id="c-name" data-id="c-name" class="form-control checkEmpty" placeholder="<?=__('نام')?>...">
                        <div class="invalid-feedback" data-id="c-name" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('دسته والد')?></label>
                        <select name="c-parent-id" id="c-parent-id" class="form-select cc-select2">
                            <option value=""><?=__('هیچکدام')?></option>
                            <?php
                            foreach ($costCategories as $costCategory) {
                            ?>
                                <option value="<?php echo $costCategory->id ?>"><?php echo $costCategory->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('کد دسته')?></label>
                        <input type="text" name="" id="c-code" class="form-control" placeholder="<?=__('کد دسته')?>...">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label"><?=__('ترتیب نمایش')?></label>
                        <input type="number" name="c-order" id="c-order" class="form-control just-numbers" placeholder="<?=__('ترتیب نمایش')?>...">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12 form-group">
                        <label class="form-label"><?=__('توضیحات')?></label>
                        <input type="text" name="c-details" id="c-details" class="form-control" placeholder="<?=__('توضیحات')?>...">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <input type="hidden" name="c-id" id="c-id" value="0">
                        <input type="hidden" name="c-action" id="c-action" value="create">
                        <button type="button" id="store-cost-category" class="btn btn-success me-sm-3 me-1 submit-by-enter"><?=__('ثبت اطلاعات')?></button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
                    </div>
                </div>
            </form>
            <hr>
            <div id="cost-category-result" class="table-responsive"><?php $cat->showIndex() ?></div>
        </div>
<?php
    }

    public function getCostPaymentsList($from_date, $to_date)
    {
        $arr = array();
        $days = (new DateTime($from_date))->diff(new DateTime($to_date))->format('%a');
        $new_from_date = $from_date->copy();
        for ($day = 1; $day <= $days; $day++) {

            $total = $this->costs()->whereDate('costs.created_at', $new_from_date)->sum('costs.price');
            array_push($arr, $total);
            $new_from_date->addDay();
        }
        return $arr;
    }
}
