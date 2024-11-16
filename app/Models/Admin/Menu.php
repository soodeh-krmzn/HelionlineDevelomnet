<?php

namespace App\Models\Admin;

use App\Models\MyModels\Admin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Admin
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'parent_id', 'name', 'icon', 'url', 'learn_url', 'details', 'display_order'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('lang', function ($builder) {
            $builder->where('lang', app()->getLocale());
        });
    }
    public function parent()
    {
        return $this->belongsTo(Menu::class, "parent_id");
    }

    public function children()
    {
        return $this->hasMany(Menu::class, "parent_id");
    }

    public function showIndex()
    {
        $menus = Menu::orderBy('display_order', 'asc')->get();
        if ($menus->count() > 0) {
?>
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th><?=__('ردیف')?></th>
                        <th>صفحه والد</th>
                        <th>برچسب</th>
                        <th>آیکن</th>
                        <th>آدرس</th>
                        <th>آدرس ویدئو آموزشی</th>
                        <th><?=__('توضیحات')?></th>
                        <th>ترتیب نمایش</th>
                        <th>مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($menus as $menu) { ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $menu->parent?->name; ?></td>
                            <td><?php echo $menu->name; ?></td>
                            <td>
                                <i class="bx bx-<?php echo $menu->icon; ?>"></i>
                            </td>
                            <td><?php echo $menu->url; ?></td>
                            <td><?php echo $menu->learn_url; ?></td>
                            <td><?php echo $menu->details; ?></td>
                            <td><?php echo $menu->display_order; ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="<?php echo $menu->id; ?>" data-bs-target="#crud" data-bs-toggle="modal">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-group" data-id="<?php echo $menu->id; ?>"><i class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        <?php
        } else { ?>
            <div class="row mx-1">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0"><?=__('موردی جهت نمایش موجود نیست.')?></div>
                </div>
            </div>
        <?php
        }
    }

    public function crud($action, $id = 0)
    {
        if ($action == "create") {
            $title = "افزودن صفحه جدید";
            $parent_id = "";
            $label = "";
            $icon = "";
            $url = "";
            $learn_url = "";
            $display_order = "";
            $details = "";
        } else if ($action == "update") {
            $title = "ویرایش مورد";
            $menu = Menu::find($id);
            $parent_id = $menu->parent_id;
            $label = $menu->name;
            $icon = $menu->icon;
            $url = $menu->url;
            $learn_url = $menu->learn_url;
            $display_order = $menu->display_order;
            $details = $menu->details;
        }
        ?>
        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="text-center mb-4 mt-0 mt-md-n2">
                <h3 class="secondary-font"><?php echo $title; ?></h3>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label required">صفحه والد</label>
                    <select name="parent_id" id="parent-id" data-id="parent-id" class="form-select">
                        <option value="0">بدون والد</option>
                        <?php
                        if ($this->getSelect()->count() > 0) {
                            foreach ($this->getSelect() as $menuRow) {
                        ?>
                                <option <?php echo ($menuRow->id == $parent_id) ? 'selected' : ''; ?> value="<?php echo $menuRow->id; ?>"><?php echo $menuRow->label; ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required">برچسب <span class="text-danger">*</span></label>
                    <input type="text" name="label" id="label" data-id="label" class="form-control checkEmpty" placeholder="برچسب..." value="<?php echo $label; ?>">
                    <div class="invalid-feedback" data-id="name" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label required">آیکن <span class="text-danger">*</span></label>
                    <input type="text" name="icon" id="icon" data-id="icon" class="form-control" placeholder="آیکن..." value="<?php echo $icon; ?>">
                    <div class="invalid-feedback" data-id="icon" data-error="checkEmpty"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required">آدرس <span class="text-danger">*</span></label>
                    <input type="text" name="url" id="url" data-id="url" class="form-control checkEmpty" style="direction: ltr;" placeholder="Route..." value="<?php echo $url; ?>">
                    <div class="invalid-feedback" data-id="url" data-error="checkEmpty"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 form-group">
                    <label class="form-label required">آدرس ویئو آموزشی</label>
                    <input type="text" name="learn_url" id="learn-url" class="form-control" placeholder="آدرس ویدئو آموزشی..." value="<?php echo $learn_url; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label required">ترتیب نمایش</label>
                    <input type="text" name="display_order" id="display-order" class="form-control" placeholder="ترتیب نمایش..." value="<?php echo $display_order; ?>">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12 form-group">
                    <label class="form-label required"><?=__('توضیحات')?></label>
                    <input type="text" name="details" id="details" class="form-control" placeholder="توضیحات..." value="<?php echo $details; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <?php
                if ($action == "create") { ?>
                    <button type="button" id="store-menu" data-action="create" class="btn btn-success me-sm-3 me-1"><?=__('ثبت اطلاعات')?></button>
                <?php
                } else {
                ?>
                    <button type="button" id="store-menu" data-id="<?php echo $id ?>" data-action="update" class="btn btn-warning me-sm-3 me-1"><?=__('ویرایش اطلاعات')?></button>
                <?php
                } ?>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close"><?=__('انصراف')?></button>
            </div>
        </div>
        <?php
    }

    public static function getSelect()
    {
        $menus = Menu::orderBy('id', 'desc')->get();
        return $menus;
    }

    public static function showMenus()
    {
        if (auth()->user()->access == 1) {
            // $packageMenus = Cache::rememberForever('package_menus', function () {
            $package = auth()->user()->account->package;
            $packageMenus = $package->menus()->where('display_nav', 1)->orderBy('display_order', 'asc')->pluck('menus.id')->toArray();
            // });
            $menus = Menu::find($packageMenus);
        } else {
            $menus = auth()->user()->group?->menus()->where('display_nav', 1)->orderBy('display_order', 'asc')->get();
        }
        if ($menus && $menus->count() > 0) {
            foreach ($menus->where('parent_id', 0) as $menu) {
        ?>
                <li class="menu-item <?php echo active_menu($menu->getAllChildrenUrl()) ?>">
                    <a href="<?php echo $menu->url ?>" class="menu-link <?php echo $menu->children()->where('display_nav', 1)->count() > 0 ? 'menu-toggle' : '' ?>">
                        <i class="menu-icon bx <?php echo $menu->icon ?>"></i>
                        <div><?php echo $menu->name ?></div>
                    </a>
                    <?php
                    $menu->showChildren($menus);
                    ?>
                </li>
            <?php
            }
        } else {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">کاربر گرامی شما به هیچ منویی دسترسی ندارید. لطفا با مدیر مجموعه تماس بگیرید.</div>
                </div>
            </div>
        <?php
        }
    }

    public function showChildren($packageMenus)
    {
        // $children = $this->children()->where('display_nav', 1)->orderBy('display_order', 'asc')->get();

        $children = $packageMenus->where('parent_id', $this->id)->where('display_nav', 1)->sortBy('display_order');
        // dd($children->toArray());
        if (auth()->user()->access == 1) {
            $menus = Menu::all();
        } else {
            $menus = auth()->user()->group?->menus;
        }
        if ($this->children->count() > 0) {
        ?>
            <ul class="menu-sub">
                <?php
                foreach ($children as $menu) {
                    if ($menus->contains($menu->id)) {
                        $routes = $menu->getAllChildrenUrl();
                        if ($menu->url != '' && $menu->url != '/') {
                            array_push($routes, $menu->url);
                        }
                ?>
                        <li class="menu-item <?php echo active_menu($routes) ?>">
                            <a href="<?php echo $menu->url ?>" class="menu-link <?php echo $menu->children()->where('display_nav', 1)->count() > 0 ? 'menu-toggle' : '' ?>">
                                <i class="menu-icon bx <?php echo $menu->icon ?>"></i>
                                <div><?php echo $menu->name ?></div>
                            </a>
                            <?php
                            $menu->showChildren($packageMenus);
                            ?>
                        </li>
                <?php
                    }
                }
                ?>
            </ul>
        <?php
        }
    }

    public function getHelp($url)
    {
        $menu = Menu::where("url", $url)->first();
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
            $label = "ناشناس";
            $icon = "";
            $url = "";
            $learn_url = "";
            $details = "";
        }
        ?>
        <div class="offcanvas-header">
            <h5 id="offcanvasStartLabel" class="offcanvas-title"><?php echo $label; ?></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <p><?php echo $details; ?></p>
            <hr>
            <div id="menu-<?php echo $id; ?>">
                <script type="text/JavaScript" src=https://www.aparat.com/embed/<?php echo $learn_url ?>?data[rnddiv]=menu-<?php echo $id ?>&data[responsive]=yes></script>
            </div>
            <hr>
            <button type="button" class="btn btn-label-secondary d-grid w-100" data-bs-dismiss="offcanvas">بستن</button>
        </div>
<?php
    }

    public function getAllChildrenUrl()
    {
        $children = [];
        foreach ($this->children as $child) {
            if ($child->url != '' && $child->url != '/') {
                array_push($children, $child->url);
            }
            $children = array_merge($children, $child->getAllChildrenUrl());
        }
        return $children;
    }
}
