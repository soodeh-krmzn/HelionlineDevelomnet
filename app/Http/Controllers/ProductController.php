<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Category;
use App\Models\EditReport;
use App\Models\Product;
use App\Services\Export;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{

    public function index()
    {
        // dd('dd');
        $product = new Product;
        return view('product.index', compact('product'));
    }

    public function crud(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $product = new Product;
        return $product->crud($action, $id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'stock' => 'required',
            'buy' => 'required',
            'sale' => 'required',
            'image' => 'nullable|max:2048|image|mimes:png,jpg',
            'categories' => 'required'
        ]);

        try {
            $action = $request->action;
            $categories = explode(",", $request->categories);

            $image_url = '';
            if ($request->image != '') {
                $image_name = now()->timestamp . '_' . $request->image->getClientOriginalName();
                $image_path = public_path('uploads');
                $request->image->move($image_path, $image_name);

                $image_url = 'uploads/' . $image_name;
            }

            if ($action == "create") {
                $product = new Product;
                $product->name = $request->name;
                $product->stock = $request->stock;
                $product->buy = $request->buy;
                $product->sale = $request->sale;
                $product->image = $image_url;
                $product->status = $request->status;
                $product->save();
            } else if ($action == "update") {
                $id = $request->id;
                $product = Product::find($id);
                $details = $this->editDetails($request, $product);

                $product->name = $request->name;
                $product->buy = $request->buy;
                $product->sale = $request->sale;
                if ($image_url != '' || $request->no_image == "true") {
                    if (File::exists($product->image)) {
                        File::delete($product->image);
                    }
                    $product->image = $image_url;
                }
                $product->status = $request->status;
                $product->save();

                if ($details != "") {
                    EditReport::create([
                        'user_id' => auth()->user()->id,
                        'edited_type' => 'App\Models\Product',
                        'edited_id' => $product->id,
                        'details' => $details
                    ]);
                }

                $stock_change = $request->stock - $product->stock;
                $product->editStock($stock_change);
            }
            $product->categories()->sync($categories);
            // $product = new Product;
            // return $product->showIndex(Product::all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);

        if (File::exists($product->image)) {
            File::delete($product->image);
        }

        $product->delete();
        // return $product->showIndex(Product::all());
    }

    public function search(Request $request)
    {
        $name = $request->name;
        $stock = $request->stock;
        $from_buy = $request->from_buy;
        $to_buy = $request->to_buy;
        $from_sale = $request->from_sale;
        $to_sale = $request->to_sale;
        $category_id = $request->category;

        $products = Product::query();

        if ($name != null) {
            $products->where('name', 'like', '%' . $name . '%');
        }
        if ($stock != null) {
            switch ($stock) {
                case '1':
                    $products->where('stock', '>', 0);
                    break;

                case '0':
                    $products->where('stock', '==', 0);
                    break;

                default:
                    break;
            }
        }
        if ($from_buy != null) {
            $products->where('buy', '>=', $from_buy);
        }
        if ($to_buy != null) {
            $products->where('buy', '<=', $to_buy);
        }
        if ($from_sale != null) {
            $products->where('sale', '>=', $from_sale);
        }
        if ($to_sale != null) {
            $products->where('sale', '<=', $to_sale);
        }
        if ($category_id != null) {
            $products->join('product_category', 'products.id', '=', 'product_category.product_id')
                ->where('product_category.category_id', $category_id);
        }
        /*
        $products = $products->latest()->get()->map(function($item) {
            $item->buy_format = cnf($item->buy);
            $item->sale_format = cnf($item->sale);
            $item->stock_format = cnf($item->stock);
            return $item;
        });
        */
        $products = $products->orderBy('products.created_at', 'desc')->get()->map(function($item) {
            $item->buy_format = cnf($item->buy);
            $item->sale_format = cnf($item->sale);
            $item->stock_format = cnf($item->stock);
            return $item;
        });
        return $products;
    }

    public function export(Request $request)
    {
        $data = $this->search($request);
        // return Export::export($data,['row','name','buy_format','sale_format','stock_format'],['ردیف','نام','قیمت خرید','قیمت فروش','موجودی']);
        return Export::export($data,['row', 'name', 'buy_format', 'sale_format', 'stock_format']);
    }

    public function crudStock(Request $request)
    {
        $id = $request->id;
        $product = new Product;
        return $product->crudStock($id);
    }

    public function updateStock(Request $request)
    {
        $id = $request->id;
        $sign = $request->sign;
        $change = $request->change;

        $product = Product::find($id);
        if ($sign == -1 && $change > $product->stock) {
            return response()->json([
                "message" => __("عدد وارد شده از موجودی محصول بیشتر است.")
            ], 400);
        }

        $product->editStock($change * $sign);

        // $products = Product::all();
        // return $product->showIndex($products);
    }

    protected function editDetails(Request $request, Product $product)
    {
        $details = "";
        if ($request->name != $product->name) {
            $details .= "تغییر نام از " . $product->name . " به " . $request->name . ", \n";
        }
        if ($request->buy != $product->buy) {
            $details .= "تغییر قیمت خرید از " . cnf($product->buy) . " به " . cnf($request->buy) . ", \n";
        }
        if ($request->sale != $product->sale) {
            $details .= "تغییر قیمت فروش از " . cnf($product->sale) . " به " . cnf($request->sale) . ", \n";
        }
        if ($request->status != $product->status) {
            $details .= "تغییر وضعیت از " . ($product->status == 1 ? "فعال" : "غیرفعال") .
                " به " . ($request->status == 1 ? "فعال" : "غیرفعال") . "\n";
        }

        return $details;
    }

    public function dataTable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('filter_search')) {
                $data = $this->search($request);
            } else {
                $data = Product::query();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('buy', '{{ cnf($buy) }}')
                ->editColumn('sale', '{{ cnf($sale) }}')
                ->editColumn('stock', function (Product $product) {
                    return $product->getStock();
                })
                ->editColumn('status', function (Product $product) {
                    return $product->status();
                })
                ->editColumn('image', function (Product $product) {
                    $img = '<img src="' . $product->image . '" class="product-image">';
                    if ($product->image != null) {
                        return $img;
                    }
                })
                ->addColumn('action', function (Product $product) {
                    $actionBtn = '';
                    if (Gate::allows('update')) {
                        $actionBtn .= '<button type="button" class="btn btn-secondary btn-sm crud-stock" data-id="' . $product->id . '" data-bs-toggle="modal" data-bs-target="#stock-modal"><i class="bx bxs-cube"></i></button>
                        <button type="button" class="btn btn-warning btn-sm crud" data-action="update" data-id="' . $product->id . '" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="bx bx-edit"></i></button>';
                    }
                    if (Gate::allows('update')) {
                        $actionBtn .= '
                        <button class="btn btn-danger btn-sm delete-product" data-id="' . $product->id . '"><i class="bx bx-trash"></i></button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'image', 'status', 'stock'])
                ->make(true);
        }
    }
}
