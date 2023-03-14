<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;


class ProductController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($id = null)
    {

        $products = \DB::table('product')
            ->select('product_id', 'unit_quantity','min_value', 'product_name', 'text_mount', 'product_code', 'category_name', 'unit_name', 'product_description')
            ->join('category', 'category.category_id', '=', 'product.category_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->where('product.status', '!=', 1)
            ->orderByDesc('product_id');

        if (isset($id) && $id > 0) {
            $products = $products->paginate($id);
        } else {
            $products = $products->paginate(5);
        }
        return view('product.index', compact('products'))->with(['panel_title' => 'لیست محصولات', "route" => route('product.list')]);
    }

    public function newProduct()
    {
        return view('product.registerNewProduct');
    }

    public function create(Request $request)
    {
        $category = Category::all()->where('status', '!=', '1');
        $unit = Unit::all()->where('status', '!=', '1');

        if ($request->isMethod('get'))

            return view('product.registerNewProduct', compact('category', 'unit'))->with(['panel_title' => 'ایجاد محصول جدید']);
        else {
            $validator = Validator::make(Input::all(), [
                    'product_name' => 'required',
                    'product_code' => 'required',
                    'product_category' => 'required',
                    'text_mount' => 'required',
                    'product_unit' => 'required',
                    'min_value' => 'required',
                    'default_product' => 'required',

                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }


        }

        $code = $request->input('product_code');
        $recourd = DB::table('product')->where('product_name', $code)->doesntExist();
        if ($recourd) {
            $product = new Product();
            $product->product_name = Input::get('product_name');
            $product->product_code = Input::get('product_code');
            $product->text_mount = Input::get('text_mount');
            $product->product_description = Input::get('product_description');
            $product->min_value = Input::get('min_value');
            $product->category_id = Input::get('product_category');
            $product->default_sale_product = Input::get('default_product');
            $product->unit_id = Input::get('product_unit');
            $product->save();
            return array(

                'content' => 'content',
                'url' => route('product.create')
            );


        }


    }

    public function update(Request $request, $id)
    {
        $category = Category::all()->where('status', '!=', '1');
        $unit = Unit::all()->where('status', '!=', '1');
        $product = Product::find($id);

        if ($request->isMethod('get'))

            return view('product.registerNewProduct', compact('product', 'category', 'unit'))->with(['panel_title' => 'ویرایش محصولات']);
        else {
            $validator = Validator::make(Input::all(), [
                    'product_name' => 'required',
                    'product_code' => 'required',
                    'product_category' => 'required',
                    'text_mount' => 'required',
                    'product_unit' => 'required',
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $data = [
                'product_name' => Input::get('product_name'),
                'product_code' => Input::get('product_code'),
                'product_description' => Input::get('product_description'),
                'text_mount' => Input::get('text_mount'),
                'category_id' => Input::get('product_category'),
                "min_value" => Input::get('min_value'),
                "default_sale_product" => Input::get('default_product'),
                'unit_id' => Input::get('product_unit'),
            ];
            Product::find($id)->update($data);
            return array(
                'content' => 'content',
                'url' => route('product.list')
            );
        }


    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            Product::find($id)->where('product_id', $id)->update(['status' => 1]);
            return redirect()->route("product.list")->with('success', 'محصول مورد نظر با موفقیت حذف شد ');
        }
    }

    public function search($id)
    {
        $data = \DB::table('product')
            ->join('category', 'category.category_id', '=', 'product.category_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->select('product_id', 'product_name', 'product_code', 'category_name', 'unit_name', 'product_description')
            ->where('product_id', 'LIKE', "%$id%")
            ->orWhere('product_buy_price', 'LIKE', "%$id%")
            ->orWhere('product_name', 'LIKE', "%$id%")
            ->orWhere('product_code', 'LIKE', "%$id%")
            ->orWhere('category_name', 'LIKE', "%$id%")
            ->orWhere('unit_name', 'LIKE', "%$id%")
            ->orWhere('product_description', 'LIKE', "%$id%")
            ->get();


        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }
    }
}
