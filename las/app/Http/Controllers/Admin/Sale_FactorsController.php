<?php

namespace App\Http\Controllers\Admin;

use App\Exchangers\Exchangers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Models\SaleReturn;
use App\Http\Resources\ProductCOllection;
use App\Http\Resources\ProductFactorCollection;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\MoneyStore;
use App\Models\Product;
use App\Models\Product_Store;
use App\Models\Product_Unit;
use App\Models\Sale_Factor;
use App\Models\Sale_Product;
use App\Models\Store;
use App\Models\UnitExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use jDate\Jalali\jDate;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;


class Sale_FactorsController extends Controller
{

    public function index($id = null)
    {

        $sale_factors = \DB::table('sale_factor')
            ->select('sale_factor_id', 'currency_name', 'customer_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
            ->join('currency', 'currency.currency_id', '=', 'sale_factor.currency_id')
            ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
            ->join('store', 'store.store_id', '=', 'sale_factor.store_id')
            ->orderBy('sale_factor_code', 'DESC')
            ->where('sale_factor.status', '!=', 1)
            ->groupBy('sale_factor_id', 'customer_name', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
            ->orderBy("sale_factor_id", "desc");


        if (!(\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)) {
            $sale_factors = $sale_factors->where('store.agency_id', '=', \Auth::user()->agency_id);

        }
        (isset($id) && $id > 0) ? $sale_factors = $sale_factors->paginate($id) : $sale_factors = $sale_factors->paginate(5);


        return view('sale_factor.index', compact('sale_factors'))->with(['panel_title' => 'لیست فاکتور های خارج شده', 'route' => route('sale_factor.list')]);

    }

    public function fetchData(Request $request)
    {

        $output = '';
        $customer_type = $request->get('customer_type');
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $customers = DB::table('customer')
                ->select('name', 'customer_type', "customer_id")
                ->where('customer_type', $customer_type)
                ->where("status", "=", "0")
                ->get();
        } else {
            $customers = DB::table('customer')
                ->select('name', 'customer_type', "customer_id")
                ->where('agency_id', '=', \Auth::user()->agency_id)
                ->where('customer_type', $customer_type)
                ->where("status", "=", "0")
                ->get();
        }


        $total_row = $customers->count();

        if ($total_row > 0) {
            $output .= '
             <option>نام مشتری</option>
            ';
            foreach ($customers as $row) {
                $output .= '
               
                <option value="' . $row->customer_id . '">' . $row->name . '</option>
                
                ';

            }

        }


        $data = array(
            'option' => $output,
            'total_data' => $total_row
        );

        //echo json_encode($data);
        return response()->json($data);


    }

    public function searchResponse(Request $request)
    {
        $name = $request->get('name');
        $store_id = $request->get('store_id');


        $fieldName = $request->get('fieldName');
        if ($request->get('fieldName') === 'pquantity') {
            $fieldName = 'product_name';
        }
        $users = DB::table('product')
            ->join('product_store', 'product.product_id', '=', 'product_store.product_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->where("$fieldName", 'LIKE', '%' . $name . '%')
            ->where('product_store.store_id', $store_id)
            ->select("product_store.store_id",'default_sale_product', DB::raw("CONCAT(product_name,' - ',buy_price) AS product_name"), 'text_mount', 'product.product_id', 'product.product_code', 'product.unit_id', 'unit_name', 'product_store.quantity', 'product_store.product_stor_id')
            ->get();

        return $users;


    }

    public function searchResponseSele(Request $request)
    {
        $name = $request->get('name');
        $store_id = $request->get('seleid');
        $fieldName = $request->get('fieldName');


        if ($request->get('fieldName') === 'pquantity') {
            $fieldName = 'product_name';
        }

        $users = DB::table('product')
            ->join('product_store', 'product.product_id', '=', 'product_store.product_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->select('product.product_name', 'product.product_id', 'product.product_code', 'product.unit_id', 'unit_name', 'product_store.quantity', 'product_store.product_stor_id')
            ->where('product_store.store_id', "=", $store_id)
            ->where("$fieldName", 'LIKE', '%' . $name . '%')
            ->get();

        return $users;

    }

    public function putID(Request $request)
    {
        $store_id = $request->get('stack_name');
        $request->session()->put('store_id', $store_id);

    }

    public function create(Request $request)
    {

        date_default_timezone_set('Asia/Kabul');

        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {

            $customers = Customer::all()->where('customer.status', '!=', 1);

        } else {

            $customers = Customer::where('agency_id', '=', \Auth::user()->agency_id)->where('customer.status', '!=', 1)->get();

        }

        $money_store = MoneyStore::all()->where("status", "0");
        $companys = Company::all()->where('company.status', '!=', 1);
        $products = Product::all()->where('product.status', '!=', 1);
        $lastFactorCode = DB::table("sale_factor")->latest("sale_factor_code")->value("sale_factor_code");


        $currencies = Currency::all()->where("status", "0");

        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {

            $stores = Store::select('store_id', 'store_name')->where('status', '!=', 1)->get();

        } else {

            $stores = Store::select('store_id', 'store_name')->where('status', '!=', 1)->where('agency_id', '=', \Auth::user()->agency_id)->get();

        }
        if ($request->isMethod('get')) {


            return view('sale_factor.test', compact('customers', 'companys', 'products', 'stores', 'lastFactorCode', 'currencies', 'money_store'));

        } else {


            $validator = Validator::make(Input::all(), [
                'pr_date' => 'required',
                'pr_payment' => 'required',
                "customer_id" => "required|not_in:0",
                "sale_factor_code" => "required",

            ]);


            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            DB::beginTransaction();

            if (count($request->product_name) > 0) {

                // Add to Sale Factor Table

                try {

                    $data = [

                        'sale_factor_code' => $request->input("sale_factor_code"),
                        'discount' => $request->input("discount"),
                        'recieption_price' => $request->get('pr_payment'),
                        'total_tex' => $request->get('total_tex'),
                        'total_price' => $request->get('pr_total'),
                        'sale_date' => $request->get('pr_date'),
                        'store_id' => $request->get('stack_name'),
                        'customer_id' => $request->get('customer_id'),
                        'customer_name' => $request->get('customer_name'),
                        'currency_main_id' => get_options("mainCurrency")->value('option_value'),
                        'time' => date("h:i a"),
                        'currency_id' => $request->get('currency_id'),
                        'currency_rate' => $request->get('currency_rate'),
                        'money_store_id' => get_options("mainMoneyStore")->value('option_value'),
                        'user_id' => Session::get('user_id')

                    ];

                    $fact = Sale_Factor::create($data);

                    $s_f_id = $fact->sale_factor_id;
                    // Update Money Store Table

                    $MoneyStore = MoneyStore::find($data['money_store_id']);

                    $mountToMoneyStore = $data['recieption_price'] * $data['currency_rate'];
                    $MoneyStore->increment('money_amount', $mountToMoneyStore);


                    // Add to Customer Payment
                    $customerPayment = new CustomerPayment();
                    $customerPayment->payment_amount = $data['recieption_price'];
                    $customerPayment->sale_factor_id = $s_f_id;
                    $customerPayment->date = $data['sale_date'];
                    $customerPayment->type = 'factorRegister';
                    $customerPayment->table_related = 'sale_factor';
                    $customerPayment->currency_id = $data['currency_id'];
                    $customerPayment->currency_rate = $data['currency_rate'];
                    $customerPayment->currency_main_id = $data['currency_main_id'];
                    $customerPayment->save();

                    // Add to Product Store
                    foreach ($request->product_name as $item => $value) {
                        // transaction

                        $products = [
                            'product_name' => $request->product_name[$item],
                            'product_code' => $request->product_code[$item],
                            'product_quantity' => $request->product_quantity[$item],
                            'product_price' => $request->product_price[$item],
                            'tex_precentage' => $request->product_tex[$item],
                            'product_total' => $request->product_total[$item],
                            'product_store_id' => $request->product_store_id[$item],
                            'product_id' => $request->product_id[$item],
                        ];

                        // Subtract the Store


                        if ($request->unit_id[$item] != $request->product_unit[$item]) {

                            $getCurrentUnitExchange = UnitExchange::where("main_unit_id", $request->unit_id[$item])
                                ->where("relate_unit_id", $request->product_unit[$item])
                                ->first();

                            $mountExchange = ($getCurrentUnitExchange->main_quentity / $getCurrentUnitExchange->quentity);

                        } else {

                            $mountExchange = 1;
                        }

                        // Validate, then create if valid
                        $productStore = Product_Store::find($products['product_store_id']);


                        $amount = ($products['product_quantity']) * $mountExchange;
                        $quantity = ($productStore->quantity);


                        $final_quantity = $quantity - $amount;


                        if ($final_quantity < 0) {

                            DB::rollBack();
                            return array(
                                'fail' => true,
                                'errors' => [
                                    $products['product_name'] =>
                                        "مقدار وارده در گدام موجود نیست"]
                            );
                        }

                        $pupdate = [
                            'store_id' => $data['store_id'],
                            'product_id' => $products['product_id'],
                            'quantity' => $final_quantity
                        ];


                        $productStore->update($pupdate);

                        $data1 = [
                            'sale_factor_id' => $s_f_id,
                            'product_id' => $products['product_id'],
                            'sale_price' => $request->get('product_price')[$item],
                            'total_price' => $request->product_total[$item],
                            'tex_precentage' => $request->product_tex[$item],
                            'sale_date' => $request->get('pr_date'),
                            'quantity' => $request->product_quantity[$item],
                            'exchange_unit_id' => $request->product_unit[$item],
                            'main_unit_id' => $request->unit_id[$item],
                        ];


                        $re = Sale_Product::create($data1);

                    }
                    $request->session()->put('products', $products);
                    $print = $request->input("printed") == 1 ? true : false;

                    DB::commit();

                } catch (\Exception $e) {

                    DB::rollBack();
                    return array(
                        'fail' => true,
                        'errors' => ["انجام نشد"]
                    );
                }


                return array(
                    'content' => 'content',
                    'print' => $print,
                    'url' => route('sale_factor.create'),
                    'url_print' => route("sale_factor.print", $s_f_id)
                );


            }

        }


    }

    public function customerPaymentList($id)
    {


        $customer = DB::table('customer_payment')
            ->join('sale_factor', 'sale_factor.sale_factor_id', 'customer_payment.sale_factor_id')
            ->where('customer_payment.sale_factor_id', $id)
            ->where('table_related', 'sale_factor');

        $customerPayments = $customer->get();

        $total = $customer->sum('payment_amount');

        $currency_id = Sale_Factor::find($id)->currency_id;
        $currency = Currency::find($currency_id)->currency_name;

        return view('sale_factor.customerPayments', compact('customerPayments', 'currency', 'total'))->with(['panel_title' => 'لیست پرداختی های فاکتور']);


    }

    public function printFactor($id)
    {

        $fp = DB::table('sale_factor')
            ->join("customer", 'customer.customer_id', 'sale_factor.customer_id')
            ->where('sale_factor_id', $id)->first();


        $pl = DB::table('sale_product')
            ->where('sale_factor_id', $id)
            ->join("product", 'product.product_id', 'sale_product.product_id')
            ->get();


        $result = array();

        foreach ($pl as $keyProduct => $valueProduct) {


            if (empty($result)) {

                array_push($result, $valueProduct);

            } else {

                foreach ($result as $key => $value) {

                    if ($value->product_id == $valueProduct->product_id && $value->exchange_unit_id == $valueProduct->exchange_unit_id) {

                        $result[$key]->quantity += $valueProduct->quantity;

                    } else {

                        array_push($result, $valueProduct);
                    }

                }


            }

        }

        $pl = $result;
        $remind = 0;
        if (is_null($fp->customer_name)) {
            $remind = DB::table("sale_factor")
                ->where("sale_factor.customer_id", "=", $fp->customer_id)
                ->where("sale_factor.status", "0")->sum(DB::raw('total_price - recieption_price - discount'));

        }


        return view('sale_factor.print', compact('fp', 'pl', 'remind'));


    }

    public function printReturnFactor($id)
    {
        $fp = DB::table('sale_factor')
            ->join("customer", 'customer.customer_id', 'sale_factor.customer_id')
            ->where('sale_factor_id', $id)->first();


        $pl = DB::table('sales_return')
            ->join('product', 'product.product_id', 'sales_return.product_id')
            ->where('sales_id', $id)
            ->select("sales_return.*","product.*")
            ->addSelect("unitprice as sale_price")
            ->addSelect("totalprice as total_price")
            ->get();

        $result = array();

        foreach ($pl as $keyProduct => $valueProduct) {


            if (empty($result)) {

                array_push($result, $valueProduct);

            } else {

                foreach ($result as $key => $value) {

                    if ($value->product_id == $valueProduct->product_id && $value->exchange_unit_id == $valueProduct->exchange_unit_id) {

                        $result[$key]->quantity += $valueProduct->quantity;

                    } else {

                        array_push($result, $valueProduct);
                    }

                }


            }

        }

        $pl = $result;
        $remind = 0;
        if (is_null($fp->customer_name)) {
            $remind = DB::table("sale_factor")
                ->where("sale_factor.customer_id", "=", $fp->customer_id)
                ->where("sale_factor.status", "0")->sum(DB::raw('total_price - recieption_price - discount'));

        }


        return view('sale_factor.print', compact('fp', 'pl', 'remind'));


    }

    public function update(Request $request, $id)
    {


        date_default_timezone_set('Asia/Kabul');
        $sale_factors = DB::table('sale_factor')
            ->join("customer", "customer.customer_id", "sale_factor.customer_id")
            ->where('sale_factor.sale_factor_id', $id)
            ->first();

        $customers = Customer::all()->where('status', '!=', 1);
        $currencies = Currency::all()->where("status", "0");
        $money_store = MoneyStore::all()->where("status", "0");

        $sale_product = DB::table('sale_product')
            ->join('product', 'product.product_id', '=', 'sale_product.product_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->where("sale_product.status", "=", "0")
            ->where('sale_factor_id', $id)
            ->distinct("sale_id")
            ->get();

        $stores = Store::select('store_id', 'store_name')->get();


        if ($request->isMethod('get')) {

            return view('sale_factor.edit_form', compact('sale_factors', 'currencies', 'customers', 'stores', "sale_product", 'money_store'));

        } else {


            // Form Validations

            if (($request->input("product_name"))) {

                foreach (array_count_values($request->input("product_name")) as $a) {

                    if ($a != 1) {

                        return array(
                            'fail' => true,
                            'errors' => ["خطا" => "جنس تان دوبار تکرار شده"]
                        );

                    }

                }

            }

            $validator = Validator::make(Input::all(), [
                'pr_date' => 'required',
                'pr_payment' => 'required',
                "sale_factors_code" => "required"
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }


            DB::beginTransaction();

            // Global Definition Object

            try {


                $saleFactor = Sale_Factor::find($id);

                //factor data
                $dataFactor = [
                    'sale_factor_code' => $request->input("sale_factors_code"),
                    'discount' => $request->input("discount"),
                    'recieption_price' => $request->get('pr_payment'),
                    'total_price' => $request->get('pr_total'),
                    'sale_date' => $request->get('pr_date'),
                    'store_id' => $request->get('stack_name'),
                    'customer_id' => $request->get('customer_id'),
                    'customer_name' => $request->get('customer_name'),
                    'currency_main_id' => get_options("mainCurrency")->value('option_value'),
                    'time' => date("h:i a"),
                    'currency_id' => $request->get('currency_id'),
                    'currency_rate' => $request->get('currency_rate'),
                    'user_id' => Session::get('user_id')
                ];


                // update customer payment
                $customerPayment = CustomerPayment::where('sale_factor_id', $saleFactor->sale_factor_id)
                    ->where('table_related', 'sale_factor')->where('type', 'factorRegister')->first();

                $customerPayment->update([
                    'payment_amount' => $dataFactor['recieption_price'],
                    'date' => $dataFactor['sale_date'],
                    'currency_id' => $dataFactor['currency_id'],
                    'currency_rate' => $dataFactor['currency_rate'],
                    'currency_main_id' => $dataFactor['currency_main_id']
                ]);


                // change Money Store

                /**  get related money store that before store money of selling products */

                $prere = $saleFactor->recieption_price;

                /**  get related money store that before store money of selling products */
                $money_current = MoneyStore::find($saleFactor->money_store_id);


                $mountToMoneyStore = $dataFactor['recieption_price'] * $dataFactor['currency_rate'];

                $mountToMoneyStoreBefore = $prere * $saleFactor->currency_rate;

                $money_current->money_amount = $money_current->money_amount + $mountToMoneyStore - $mountToMoneyStoreBefore;
                $money_current->save();


                // Check if Stack changed or not

                if ($dataFactor['store_id'] == $saleFactor->store_id) {


                    // get ids of already saved in sale product
                    $arr = DB::table("sale_product")->where("sale_factor_id", $id)->pluck("sale_id")->toArray();


                    // delete the product that not in list
                    if (count($request->product_sale_id) > 0) {

                        $id_come = $request->product_sale_id;
                        // find the differnt of product already and new form submited
                        $def = array_diff($arr, $id_come);

                        if (count($def) > 0) {

                            foreach ($def as $d) {

                                $del = DB::table("sale_product")->where("sale_id", $d)->select("product_id", "quantity")->first();
                                $pr = DB::table("product_store")->where("store_id", $saleFactor->store_id)
                                    ->where("product_id", $del->product_id);
                                $pr->update(["quantity" => ($pr->value("quantity") + $del->quantity)]);
                                DB::table("sale_product")->where("sale_id", $d)->delete();
                            }
                        }

                    }

                    //Adding the new Products
                    foreach ($request->product_name as $item => $value) {


                        if (isset($request->product_sale_id[$item])) {


                            $product_sale_id = $request->product_sale_id[$item];

                            $products = [
                                'sale_factor_id' => $id,
                                'product_id' => $request->product_id[$item],
                                'quantity' => $request->product_quantity[$item],
                                'sale_price' => $request->product_price[$item],
                                'tex_precentage' => $request->product_tex[$item],
                                'total_price' => $request->product_total[$item],
                            ];

                            $p = DB::table("product_store")
                                ->where("store_id", $saleFactor->store_id)
                                ->where("product_id", $products['product_id']);

                            $prv = Sale_Product::find($product_sale_id);

                            $current_qu = $prv->quantity - $products["quantity"];

                            if ($current_qu > 0) {

                                // 50
                                $p->update([
                                    "quantity" => $current_qu + $p->value("quantity"),
                                ]);

                            } elseif ($current_qu < 0) {

                                if ($p->value("quantity") < $prv->quantity) {

                                    return array(
                                        'fail' => true,
                                        'errors' => ["خطا" => " مقدار وارده د گدام موجود نیست"]
                                    );

                                } else {
                                    $p->update([
                                        "quantity" => $p->value("quantity") + $current_qu,
                                    ]);

                                }

                            }

                            $table = \DB::table('sale_product')->where('sale_id', $product_sale_id);
                            $table->update($products);

                        } else {


                            $product_name = $request->product_name[$item];

                            $price = Product::find($request->product_id[$item]);


                            $products = [

                                'product_id' => $request->product_id[$item],
                                'sale_factor_id' => $id,
                                'quantity' => $request->product_quantity[$item],
                                'sale_price' => $request->product_price[$item],
                                'tex_precentage' => $request->product_tex[$item],
                                'total_price' => $request->product_total[$item],
                                'sale_date' => $request->get('pr_date'),
                            ];


                            $p = DB::table("product_store")
                                ->where("store_id", $saleFactor->store_id)
                                ->where("product_id", $products['product_id']);


                            $p->update([

                                "quantity" => $p->value("quantity") - $products["quantity"],

                            ]);

                            Sale_Product::create($products);

                        }


                    }


                } else {


                    $preproduct = DB::table("sale_product")
                        ->where("sale_factor_id", $id);

                    $prev_product = $preproduct->get();

                    foreach ($prev_product as $prev) {

                        $product_store = DB::table("product_store")
                            ->where("store_id", $saleFactor->value("store_id"))
                            ->where("product_id", $prev->product_id);

                        $product_store->update([
                            "quantity" => $product_store->value("quantity") + $prev->quantity,
                        ]);

                    }

                    $preproduct->delete();


                    foreach ($request->product_name as $item => $value) {


                        $product_id = $request->product_id[$item];

                        $amount = $request->product_quantity[$item];


                        $product_store = DB::table("product_store")
                            ->where("store_id", $saleFactor->store_id)
                            ->where("product_id", $product_id);

                        // change store have problem need to fix

                        $quantity = $product_store->value("quantity");


                        $amount = floatval($amount);
                        $quantity = floatval($quantity);

                        $final_quantity = $quantity - $amount;

                        $pupdate = [
                            'store_id' => $saleFactor->store_id,
                            'product_id' => $product_id,
                            'quantity' => $final_quantity
                        ];

                        $product_store->update($pupdate);
                        $price = Product::find($product_id);
                        $data1 = [
                            'sale_factor_id' => $id,
                            'product_id' => $product_id,
                            'sale_price' => $request->get('product_price')[$item],
                            'total_price' => $request->product_total[$item],
                            'tex_precentage' => $request->product_tex[$item],
                            'quantity' => $request->product_quantity[$item],
                            'sale_date' => $request->get('pr_date'),
                        ];

                        Sale_Product::create($data1);


                    }

                }

                // Update Factor
                $saleFactor->update($dataFactor);

//
                DB::commit();
                return array(
                    'content' => 'content',
                    'url' => route('sale_factor.list')
                );


            } catch (\Exception $e) {

                DB::rollBack();
                return array(
                    'fail' => true,
                    'errors' => ["انجام نشد"]
                );

            }


        }


    }

    public function returnFactor(Request $request, $id)
    {

        $sale_factors = DB::table('sale_factor')
            ->join("customer", "customer.customer_id", "sale_factor.customer_id")
            ->where('sale_factor.sale_factor_id', $id)
            ->first();
        $customers = Customer::all()->where('status', '!=', 1);

        $currencies = Currency::all()->where("status", "0");
        $money_store = MoneyStore::all()->where("status", "0");

        $sale_product = DB::table('sales_return')
            ->join('product', 'product.product_id', '=', 'sales_return.product_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->where('sales_return.sales_id', $id)
            ->get();

        $stores = Store::select('store_id', 'store_name')->get();

        $sale_return = DB::table('sales_return')
            ->where('sales_return.sales_id', $id)
            ->sum('totalprice');

        if ($request->isMethod('get')) {


            return view('sale_factor.return_products', compact('sale_factors', 'sale_return', 'currencies', 'customers', 'stores', "sale_product", 'money_store'));

        } else {


            if (($request->input("product_name"))) {

                foreach (array_count_values($request->input("product_name")) as $a) {

                    if ($a != 1) {

                        return array(
                            'fail' => true,
                            'errors' => ["خطا" => "جنس تان دوبار تکرار شده"]
                        );

                    }

                }

            }


            $validator = Validator::make(Input::all(), [
                'pr_date' => 'required',
                'pr_total' => 'required'
            ]);


            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }


            foreach ($request->product_name as $item => $value) {

                $products = [
                    'product_id' => $request->product_id[$item],
                    'product_quantity' => $request->product_quantity[$item],
                ];

                $quantity = DB::table("sale_product")->where("sale_factor_id", $id)->where("product_id", $products['product_id'])->value('quantity');
                if ($quantity == null) {
                    return array(
                        'fail' => true,
                        'errors' => ["خطا" => getProductName($products['product_id'])
                            . " در فاکتور قبلی موجود نیست"
                        ],

                    );
                }
                if (($quantity != null) && ($quantity < $products['product_quantity'])) {
                    return array(
                        'fail' => true,
                        'errors' => ["خطا" => getProductName($products['product_id'])
                            . "در فاکتور ثبت شده زیاد میباشد" .
                            "حد اعظم تعداد جنس از " .
                            $quantity .
                            'بیشتر نباشد!',
                        ],
                    );
                }

            }

            $sale_return_total = DB::table('sales_return')
                ->where('sales_return.sales_id', $id)
                ->sum('totalprice');


            $prefactor = DB::table("sale_factor")->where("sale_factor_id", $id);


            // Update Money Store Table
            $MoneyStore = MoneyStore::find($prefactor->value('money_store_id'));

            $mountToMoneyStore = Input::get('pr_total') * $prefactor->value('currency_rate');
            $mountToMoneyStorebefore = $sale_return_total * $prefactor->value('currency_rate');


            $MoneyStore->increment('money_amount', $mountToMoneyStorebefore);
            $MoneyStore->decrement('money_amount', $mountToMoneyStore);


            DB::table('customer_payment')->where('sale_factor_id', $id)->where('type', 'ReturnFactor')->delete();

            // Add to Customer Payment
            $customerPayment = new CustomerPayment();
            $customerPayment->payment_amount = Input::get('pr_total');
            $customerPayment->sale_factor_id = $id;
            $customerPayment->date = Input::get('pr_date');
            $customerPayment->type = "ReturnFactor";
            $customerPayment->table_related = 'buy_factor';

            $customerPayment->currency_id = $prefactor->value('currency_id');
            $customerPayment->currency_rate = $prefactor->value('currency_rate');
            $customerPayment->currency_main_id = $prefactor->value('currency_main_id');

            $customerPayment->save();


            $getreturnList = DB::table('sales_return')
                ->where('sales_return.sales_id', $id);

            $listProduct = $getreturnList->get();


            foreach ($listProduct as $key => $list) {

                $productStore = DB::table('product_store')
                    ->where("store_id", $prefactor->value('store_id'))
                    ->where('product_id', $list->product_id);


                if ($request->unit_id[$key] != $request->product_unit[$key]) {

                    $getCurrentUnitExchange = UnitExchange::where("main_unit_id", $request->unit_id[$key])
                        ->where("relate_unit_id", $request->product_unit[$key])
                        ->first();

                    $mountExchange = ($getCurrentUnitExchange->main_quentity / $getCurrentUnitExchange->quentity);

                } else {

                    $mountExchange = 1;
                }

                $productStore->update([
                    'quantity' => $productStore->value('quantity') - ($list->quantity * $mountExchange)
                ]);

            }

            $getreturnList->delete();

            foreach ($request->product_name as $item => $value) {

                $products = [
                    'total_price' => $request->product_total[$item],
                    'product_id' => $request->product_id[$item],
                    'product_quantity' => $request->product_quantity[$item],
                    'product_price' => $request->product_price[$item],
                    'product_total' => $request->product_total[$item],
                ];

                $new = new SaleReturn();

                $productStore = DB::table('product_store')
                    ->where("store_id", $prefactor->value('store_id'))
                    ->where('product_id', $products['product_id']);


                if ($request->unit_id[$item] != $request->product_unit[$item]) {

                    $getCurrentUnitExchange = UnitExchange::where("main_unit_id", $request->unit_id[$item])
                        ->where("relate_unit_id", $request->product_unit[$item])
                        ->first();

                    $mountExchange = ($getCurrentUnitExchange->main_quentity / $getCurrentUnitExchange->quentity);

                } else {

                    $mountExchange = 1;
                }


                $productStore->update([
                    'quantity' => $productStore->value('quantity') + ($products['product_quantity'] * $mountExchange)
                ]);

                $products = [
                    'total_price' => $request->product_total[$item],
                    'product_id' => $request->product_id[$item],
                    'product_quantity' => $request->product_quantity[$item],
                    'product_price' => $request->product_price[$item],

                    'product_price' => $request->product_price[$item],
                    'product_total' => $request->product_total[$item],
                ];


                $new->sales_id = $id;
                $new->product_id = $products["product_id"];
                $new->quantity = $products['product_quantity'];
                $new->return_date = $request->input('pr_date');
                $new->unitprice = $products['product_price'];
                $new->totalprice = $products['product_total'];
                $new->exchange_unit_id = $request->product_unit[$item];
                $new->main_unit_id = $request->unit_id[$item];
                $new->save();

            }

            $request->session()->put('products', $products);
            $print = $request->input("printed") == 1 ? true : false;
            return array(
                'content' => 'content',
                'print' => $print,
                'url' => route('sale_factor.list'),
                'url_print' => route("sale_factor.printReturnFactor", $id)
            );


        }

    }

    public function get_details(Request $request, $id)
    {
        $request->session()->put('sale_factor_id', $id);

        $sale_title = Sale_Factor::select('sale_factor_id', 'sale_factor_code', 'total_price', 'sale_date', 'customer.name as cname')
            ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
            ->where('sale_factor.status', '!=', 1)
            ->where("sale_factor_id", "=", $id)
            ->first();


        $sale_factors = \DB::table('sale_product')
            ->join('product', 'product.product_id', '=', 'sale_product.product_id')
            ->select('product.product_name', 'sale_product.package_price', 'sale_product.sale_date', 'sale_product.quantity', 'sale_product.store_id')
            ->where('sale_factor_id', $id)
            ->get();

        return view('sale_factor.details', compact('sale_factors', "sale_title"))->with('panel_title', 'جزئیات فکتورهای خارج شده ازگذام');

    }

    public function delete($id)
    {


        if ($id && ctype_digit($id)) {
            $sale_p_id = DB::table('sale_product')
                ->select('sale_id')
                ->where('sale_factor_id', $id)->get();
            $ides = array();
            foreach ($sale_p_id as $key => $value) {
                $ides[$key] = $value->sale_id;
            }
            foreach ($ides as $i) {
                Sale_Product::find($i)->where('sale_id', $i)->update(['status' => 1]);

            }
            $user = Sale_Factor::find($id)->where('sale_factor_id', $id)->update(['status' => 1]);
            return redirect()->route('sale_factor.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');

        }

    }

    public function report()
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $stores = Store::all();
        } else {
            $stores = Store::where('agency_id', '=', \Auth::user()->agency_id)->get();
        }
        return view('sale_factor.report', compact('stores'))->with('panel_title', 'گزارش فاکتور های فروش');

    }

    public function report_data(Request $request, $id = null)
    {
        if ($request->ajax()) {


            $output = '';
            $data = '';
            $sum = 0;
            $store_id = $request->get('stack_name');
            $type = $request->get('type');
            $y = $request->get('year');


            if ($store_id === 'all') {

                if (isset($id) && $id > 0) {
                    $data = \DB::table('sale_factor')
                        ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                        ->paginate($id);
                } else {
                    $data = \DB::table('sale_factor')
                        ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                        ->paginate(5);
                }

                $sum = \DB::table('sale_factor')
                    ->sum('total_price');


                if ($type === 'day') {


                    if (\Auth::user()->user_level == 1 && \Auth::user()->agnecy_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate(5);
                        }
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate(5);
                        }
                    }

                    $sum = \DB::table('sale_factor')
                        ->where('sale_date', date("Y-m-d"))
                        ->sum('total_price');

                } elseif ($type === 'week') {

                    $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                    $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');

                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate(5);
                        }
                    } else {

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate(5);
                        }

                    }

                    $sum = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                        ->sum('total_price');

                } elseif ($type === 'month') {

                    $get_month = $request->get('month_r');
                    if (ctype_digit($get_month)) {
                        $jyear = date("Y");
                        $jmonth = $get_month;
                        $jday = '1';
                        $start_date = '';
                        if ($jmonth < 10 && $jday > 9) {
                            $start_date = $jyear . '-0' . $jmonth . '-' . $jday;
                        } elseif ($jday < 10 && $jmonth > 9) {
                            $start_date = $jyear . '-' . $jmonth . '-0' . $jday;

                        } elseif ($jmonth < 10 && $jday < 10) {
                            $start_date = $jyear . '-0' . $jmonth . '-0' . $jday;
                        } else {
                            $start_date = $jyear . '-' . $jmonth . '-' . $jday;
                        }
                        // end date
                        $end_date = '';
                        $end_day = '31';
                        if ($jmonth < 10 && $end_day > 9) {
                            $end_date = $jyear . '-0' . $jmonth . '-' . $end_day;
                        } elseif ($end_day < 10 && $jmonth > 9) {
                            $end_date = $jyear . '-' . $jmonth . '-0' . $end_day;

                        } elseif ($jmonth < 10 && $end_day < 10) {
                            $end_date = $jyear . '-0' . $jmonth . '-0' . $end_day;
                        } else {
                            $end_date = $jyear . '-' . $jmonth . '-' . $end_day;
                        }

                        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    //->where('expens_reason_id', $reason)
                                    ->paginate($id);
                            } else {

                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    //->where('expens_reason_id', $reason)
                                    ->paginate(5);
                            }
                        } else {
                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    //->where('expens_reason_id', $reason)
                                    ->paginate($id);
                            } else {

                                $data = \DB::table('sale_factor')
                                    ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    //->where('expens_reason_id', $reason)
                                    ->paginate(5);
                            }
                        }


                        $sum = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            //->where('expens_reason_id', $reason)
                            ->sum('total_price');
                    }

                } elseif ($type === 'year') {
                    $getyear = $request->get('year_r');
                    $yaer_date = explode('-', $getyear);
                    $final_year = $yaer_date[0];
                    $startfrom = $final_year . '-01-01';
                    $end = $final_year . '-12-31';

//                    dd($getyear);
                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->whereBetween('sale_date', [$startfrom, $end])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->whereBetween('sale_date', [$startfrom, $end])
                                ->paginate(5);
                        }
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$startfrom, $end])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$startfrom, $end])
                                ->paginate(5);
                        }
                    }

                    $sum = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$startfrom, $end])
                        ->sum('total_price');

                } elseif ($type === 'bt_date') {
                    if ($request->get('start_date') != '') {
                        $start_date = $request->get('start_date');
                        $end_date = $request->get('end_date');

                        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }
                        } else {
                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = \DB::table('sale_factor')
                                    ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }
                        }

                        $sum = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');

                    }

                }
            } else {
                if (ctype_digit($store_id)) {
                    if ($type === 'day') {

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_factor.store_id', $store_id)
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_factor.store_id', $store_id)
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate(5);
                        }

                        $sum = \DB::table('sale_factor')
                            ->where('sale_factor.store_id', $store_id)
                            ->where('sale_date', date("Y-m-d"))
                            ->sum('total_price');


                    } elseif ($type === 'week') {

                        $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                        $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_factor.store_id', $store_id)
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_factor.store_id', $store_id)
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate(5);
                        }

                        $sum = \DB::table('sale_factor')
                            ->where('sale_factor.store_id', $store_id)
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->sum('total_price');


                    } elseif ($type === 'month') {

                        $get_month = $request->get('month_r');
                        if (ctype_digit($get_month)) {
                            $jyear = date("Y");
                            $jmonth = $get_month;
                            $jday = '1';
                            $start_date = '';
                            if ($jmonth < 10 && $jday > 9) {
                                $start_date = $jyear . '-0' . $jmonth . '-' . $jday;
                            } elseif ($jday < 10 && $jmonth > 9) {
                                $start_date = $jyear . '-' . $jmonth . '-0' . $jday;

                            } elseif ($jmonth < 10 && $jday < 10) {
                                $start_date = $jyear . '-0' . $jmonth . '-0' . $jday;
                            } else {
                                $start_date = $jyear . '-' . $jmonth . '-' . $jday;
                            }
                            // end date
                            $end_date = '';
                            $end_day = '31';
                            if ($jmonth < 10 && $end_day > 9) {
                                $end_date = $jyear . '-0' . $jmonth . '-' . $end_day;
                            } elseif ($end_day < 10 && $jmonth > 9) {
                                $end_date = $jyear . '-' . $jmonth . '-0' . $end_day;

                            } elseif ($jmonth < 10 && $end_day < 10) {
                                $end_date = $jyear . '-0' . $jmonth . '-0' . $end_day;
                            } else {
                                $end_date = $jyear . '-' . $jmonth . '-' . $end_day;
                            }

                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('sale_factor.store_id', $store_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('sale_factor.store_id', $store_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }


                            $sum = \DB::table('sale_factor')
                                ->where('sale_factor.store_id', $store_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('total_price');
                        }


                    } elseif ($type === 'year') {

                        $getyear = $request->get('year_r');
//                        dd($getyear);
                        $yaer_date = explode('-', $getyear);
                        $final_year = $yaer_date[0];
                        $startfrom = $final_year . '-01-01';
                        $end = $final_year . '-12-31';

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_factor.store_id', $store_id)
                                ->whereBetween('sale_date', [$startfrom, $end])
                                ->paginate($id);
                        } else {

                            $data = \DB::table('sale_factor')
                                ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                ->where('sale_factor.store_id', $store_id)
                                ->whereBetween('sale_date', [$startfrom, $end])
                                ->paginate(5);
                        }

                        $sum = \DB::table('sale_factor')
                            ->where('sale_factor.store_id', $store_id)
                            ->whereBetween('sale_date', [$startfrom, $end])
                            ->sum('total_price');

                    } elseif ($type === 'bt_date') {
                        if ($request->get('start_date') != '') {
                            $start_date = $request->get('start_date');
                            $end_date = $request->get('end_date');

                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('sale_factor.store_id', $store_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = \DB::table('sale_factor')
                                    ->select('sale_factor_id', 'sale_factor_code', 'recieption_price', 'total_price', 'sale_date')
                                    ->where('sale_factor.store_id', $store_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }

                            $sum = \DB::table('sale_factor')
                                ->where('sale_factor.store_id', $store_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('total_price');
                        }

                    }

                }
            }

            $request->session()->put('data', $data);
            $request->session()->put('sum', $sum);


            return Response::JSON(array(
                'table_data' => $data,
                "sum" => $sum,
//                'total_data' => $total_row,
                'pagination' => (string)$data->links()
            ));

        }

    }



    public function currencyExchanger(Request $request)
    {

        if (get_options('mainCurrency')->value('option_value') == $request->input('currency_id')) {

            return 1;

        } else {

            $bproducts = \DB::table('currency_exchange')
                ->where('other_currency_id', get_options('mainCurrency')->value('option_value'))
                ->where('main_currency_id', $request->input('currency_id'))->first();

            $bproducts = $bproducts->exchange_rate / $bproducts->money_amount;
            return fmod($bproducts, 1) !== 0.00 ? \response()->json(number_format((float)$bproducts, 4, '.', '')) : $bproducts;

        }
    }

    public function currencyExchangerByCurrency(Request $request)
    {


        $moneyStore = MoneyStore::find($request->input('account_id'));


        if ($moneyStore->currency_id == $request->input('currency_id')) {

            return 1;

        } else {

            $bproducts = \DB::table('currency_exchange')
                ->where('other_currency_id', $moneyStore->currency_id)
                ->where('main_currency_id', $request->input('currency_id'))->first();

            $bproducts = $bproducts->exchange_rate / $bproducts->money_amount;

            return fmod($bproducts, 1) !== 0.00 ? \response()->json(number_format((float)$bproducts, 4, '.', '')) : $bproducts;

        }
    }

    public function getDetailsFactore($id)
    {

        $saleProduct = DB::table("sale_product")
            ->join("product", "product.product_id", "=", "sale_product.product_id")
            ->where("sale_factor_id", $id)
            ->select("sale_product.*", "product.product_name")
            ->get();


        $stores = Store::all()->where("status", "0");

        Session::put("factor_id", $id);
        return view("sale_factor.showDetailFactor", compact("saleProduct", "stores"));


    }

    public function details($id)
    {

        $bproducts = \DB::table('sale_product')
            ->join('product', 'sale_product.product_id', '=', 'product.product_id')
            ->select('product.product_name', 'sale_product.quantity', 'sale_product.sale_price', 'sale_id')->where('sale_factor_id', '=', $id)->get();
        return response()->json($bproducts);

    }

    public function productUpdate(Request $request)
    {

        $validator = Validator::make(Input::all(), [
            'count' => 'required',
            'total_price' => 'required',
        ]);
        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        }
        $data = [

            'total_price' => $request->input("total_price"),
            'quantity' => $request->input("count"),
        ];


        $sale = Sale_Factor::find(Session::get("factor_id"))->store_id;


        $prvVal = Sale_Product::find($request->input("sale_product_id"))->value("quantity");


        $current_qu = $prvVal - $request->input("count");


        $p = DB::table("product_store")
            ->where("store_id", $sale)
            ->where("product_id", $request->input("product_id"));
        if ($current_qu > 0) {


            // 50

            $p->update([

                "quantity" => $current_qu + $p->value("quantity"),

            ]);

        } else {

            //-50
            if ($p->value("quantity") < $prvVal) {


                return array(
                    'fail' => true,
                    'errors' => ["خطا" => " مقدار وارده د گدام موجود نیست"]
                );


            } else {

                $p->update([

                    "quantity" => $p->value("quantity") + $current_qu,

                ]);


            }

        }

        $table = \DB::table('sale_product')
            ->where('sale_id', $request->input("sale_product_id"));


        $table->update($data);


        return array(
            'content' => 'content',
            'url' => route('sale.getDetail', Session::get("factor_id"))
        );

    }

    public function search($id)
    {


        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $data = \DB::table('sale_factor')
                ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
                ->select('sale_factor_id', 'sale_factor_code', 'total_price', 'sale_date', 'customer.name')
                ->where('sale_factor.sale_factor_id', 'LIKE', "%$id%")
                ->where('sale_factor.sale_factor_code', 'LIKE', "%$id%")
                ->where('sale_factor.total_price', 'LIKE', "%$id%")
                ->where('sale_factor.sale_date', 'LIKE', "%$id%")
                ->orWhere('customer.name', 'LIKE', "%$id%")
                ->where('sale_factor.status', '!=', 1)
                ->get();
        } else {
            $data = \DB::table('sale_factor')
                ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
                ->select('sale_factor_id', 'sale_factor_code', 'total_price', 'sale_date', 'customer.name')
                ->where('customer.agency_id', '=', \Auth::user()->agency_id)
                ->where(function ($q) use ($id) {
                    $q->where('sale_factor.sale_factor_id', 'LIKE', "%$id%")
                        ->where('sale_factor.sale_factor_code', 'LIKE', "%$id%")
                        ->where('sale_factor.total_price', 'LIKE', "%$id%")
                        ->where('sale_factor.sale_date', 'LIKE', "%$id%")
                        ->orWhere('customer.name', 'LIKE', "%$id%");
                })
                ->where('sale_factor.status', '!=', 1)
                ->get();
        }

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }
    }

    public function filterFactors(Request $request)
    {

        $reason = $request->input('search-reason');
        $key = $request->input('keyword');


        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $sale_factors = \DB::table('sale_factor')
                    ->select('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
                    ->join('currency', 'currency.currency_id', '=', 'sale_factor.currency_id')
                    ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
                    ->join('store', 'store.store_id', '=', 'sale_factor.store_id')
                    ->where('sale_factor.status', '!=', 1);

                if ($reason == "byCodeNumber") {

                    $sale_factors = $sale_factors->where('sale_factor.sale_factor_code', 'LIKE', "%$key%");

                } else if ($reason === 'byCustomerCode') {

                    $sale_factors = $sale_factors->where('customer.customer_code', 'LIKE', "%$key%");

                } else {

                    $sale_factors = $sale_factors->where('customer.phone', 'LIKE', "%$key%");
                }


                $sale_factors = $sale_factors
                    ->groupBy('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
                    ->orderBy("sale_factor_id", "desc")
                    ->paginate($id);


            } else {

                $sale_factors = \DB::table('sale_factor')
                    ->select('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
                    ->join('currency', 'currency.currency_id', '=', 'sale_factor.currency_id')
                    ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
                    ->join('store', 'store.store_id', '=', 'sale_factor.store_id')
                    ->where('sale_factor.status', '!=', 1);
                if ($reason == "byCodeNumber") {

                    $sale_factors = $sale_factors->where('sale_factor.sale_factor_code', 'LIKE', "%$key%");

                } else if ($reason === 'byCustomerCode') {

                    $sale_factors = $sale_factors->where('customer.customer_code', 'LIKE', "%$key%");

                } else {

                    $sale_factors = $sale_factors->where('customer.phone', 'LIKE', "%$key%");

                }


                $sale_factors = $sale_factors
                    ->groupBy('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')->orderBy("sale_factor_id", "desc")
                    ->paginate(5);

            }


        } else {

            if (isset($id) && $id > 0) {
                $sale_factors = \DB::table('sale_factor')
                    ->select('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
                    ->join('currency', 'currency.currency_id', '=', 'sale_factor.currency_id')
                    ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
                    ->join('store', 'store.store_id', '=', 'sale_factor.store_id')
                    ->where('store.agency_id', '=', \Auth::user()->agency_id);

                if ($reason == "byCodeNumber") {

                    $sale_factors = $sale_factors->where('sale_factor.sale_factor_code', 'LIKE', "%$key%");

                } else if ($reason === 'byCustomerCode') {

                    $sale_factors = $sale_factors->where('customer.customer_code', 'LIKE', "%$key%");

                } else {

                    $sale_factors = $sale_factors->where('customer.phone', 'LIKE', "%$key%");

                }


                $sale_factors = $sale_factors->groupBy('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')->orderBy("sale_factor_id", "desc")
                    ->paginate($id);


            } else {

                $sale_factors = \DB::table('sale_factor')
                    ->select('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
                    ->join('customer', 'customer.customer_id', '=', 'sale_factor.customer_id')
                    ->join('currency', 'currency.currency_id', '=', 'sale_factor.currency_id')
                    ->join('store', 'store.store_id', '=', 'sale_factor.store_id')
                    ->where('store.agency_id', '=', \Auth::user()->agency_id);
                if ($reason == "byCodeNumber") {

                    $sale_factors = $sale_factors->where('sale_factor.sale_factor_code', 'LIKE', "%$key%");

                } else if ($reason === 'byCustomerCode') {

                    $sale_factors = $sale_factors->where('customer.customer_code', 'LIKE', "%$key%");

                } else {

                    $sale_factors = $sale_factors->where('customer.phone', 'LIKE', "%$key%");

                }


                $sale_factors = $sale_factors
                    ->groupBy('sale_factor_id', 'currency_name', 'discount', 'sale_factor_code', 'total_price', 'recieption_price', 'sale_date', 'customer.name', 'store.store_name')
                    ->orderBy("sale_factor_id", "desc")
                    ->paginate(5);
            }
        }

        return Response()->json($sale_factors);

    }

    public function searchStackProduct(Request $request)
    {

        $bproducts = \DB::table('product_store')
            ->join('product', 'product.product_id', '=', 'product_store.product_id')
            ->where('store_id', $request->input('store_id'))
            ->select('product.product_id', 'product_name')->get();

        return \response()->json($bproducts);

    }

    public function getUnits(Request $request)
    {

        $re = DB::table("unit_exchange")
            ->join("product_unit", "unit_id", "=", "relate_unit_id")
            ->where("main_unit_id", $request->input("unit_id"))->get();
        return \response()->json($re);
    }

    public function putMount(Request $request)
    {


        $productStore = Product_Store::find($request->input('product_store_id'));


        if (!is_null($request->input("main_id"))) {

            $re = DB::table("unit_exchange")
                ->where("main_unit_id", $request->input("main_id"))
                ->where("relate_unit_id", $request->input("related_unit"))
                ->first();


            $totalConvert = $productStore->quantity * ($re->quentity / $re->main_quentity);


            return \response()->json($totalConvert);
        } else {


            return \response()->json($productStore->quantity);

        }


    }
//    public function putMount(Request $request)
//    {
//
//
//
//        $productStore = Product_Store::find($request->input('product_store_id'));
//
//
//
//        if (! is_null($request->input("main_id"))  ){
//
//            $re = DB::table("unit_exchange")
//                ->where("main_unit_id", $request->input("related_unit"))
//                ->where("relate_unit_id", $request->input("main_id"))
//                ->first();
//
//            dd($re);
//
//            $totalConvert = $productStore->quantity * (   $re->quentity  / $re->main_quentity);
//
//
//            return \response()->json( $totalConvert);
//        }else{
//
//
//            return \response()->json( $productStore->quantity);
//
//        }
//
//
//    }

}


