<?php

namespace App\Http\Controllers\Admin;

use App\Models\BuyProduct;
use App\Models\Company;
use App\Models\BuyFactor;
use App\Models\Currency;
use App\Models\CustomerPayment;
use App\Models\MoneyStore;
use App\Models\Product;
use App\Models\Product_Store;
use App\Models\Sale_Factor;
use App\Models\Store;
use FontLib\Table\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use jDate\jalali\Facades\Jdate;
use Morilog\Jalali\Jalalian;


class BuyFactorController extends Controller
{
    public function index()
    {
        $company = Company::all()->where('status', '!=', 1)->sortByDesc('buy_factor_id');

        return view('buy_factor.index', compact('company'))->with('panel_title', 'لیست فکتورهای خریداری شده');

    }

    public function searchFactorByCompany(Request $request)
    {


        $company_id = $request->get('company_id');

        $buyFactores = DB::table('buy_factor')
            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
            ->orderBy('factor_code',"DESC")
            ->where('buy_factor.company_id', '=', $company_id)
            ->where('buy_factor.status', '!=', 1);


        if (!(\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)) {
            $buyFactores = $buyFactores->where('store.agency_id', '=', \Auth::user()->agency_id);
        }
        ($request->input('mount') > 0) ? $buyFactores = $buyFactores->paginate($request->input('mount')) : $buyFactores = $buyFactores->paginate(5);

        $qty = 0;


        return \Response::JSON(array(
            'total' => $qty,
            'data' => $buyFactores,
            'pagination' => (string)$buyFactores->links(),
        ));


    }

    public function details($id)
    {
        $bproducts = \DB::table('buy_product')
            ->join('product', 'buy_product.product_id', '=', 'product.product_id')
            ->select('product.product_name', 'buy_product.quantity', 'buy_product.buy_price', 'buy_factor_id')->where('buy_factor_id', '=', $id)->get();

        return response()->json($bproducts);
    }


    public function edit(Request $request, $id)
    {
        if ($request->isMethod('get')) {

            $bfactores = BuyFactor::all()->where('status', '!=', 1);
            $stores = Store::all()->where('status', '!=', 1);
            $factor = BuyFactor::find($id);
            $buy_factor = \DB::table('buy_product')
                ->join('product', 'buy_product.product_id', '=', 'product.product_id')
                ->join('product_store', 'product.product_id', '=', 'product_store.product_id')
                ->select('buy_product.buy_price', 'buy_product.quantity', 'product.product_name', 'product.product_code', 'buy_product.product_id', 'product_store.product_stor_id', 'buy_product.buy_product_id')
                ->where('buy_factor_id', '=', $id)->get();

            return view('buy_factor.edit-form', compact('bfactores', 'stores', 'buy_factor', 'id', 'factor'));
        } else {


            $validator = Validator::make(Input::all(), [

                'product_bprice' => 'required',
                'product_price' => 'required',
                'product_quantity' => 'required',

            ]);
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray(),
                );
            }
            if (count($request->product_name) > 0) {

                foreach ($request->product_name as $item => $value) {
                    $product_store = $request->product_store_id[$item];
                    $product_quantity = $request->product_quantity[$item];
                    $buy_product_id = $request->buy_product_id[$item];
                    $quantity = BuyProduct::find($buy_product_id);
                    if ($quantity->quantity == $request->product_quantity[$item]) {


                    } else if ($request->product_quantity[$item] > $quantity->quantity) {
                        $form = $request->product_quantity[$item];
                        $dqty = $quantity->quantity;
                        $final = $form - $dqty;

                        \DB::table('product_store')->where('product_stor_id', '=', $product_store)->increment('quantity', $final);

                    } else {
                        $dqty = $quantity->quantity;
                        $form = $request->product_quantity[$item];
                        $final = $dqty - $form;

                        \DB::table('product_store')->where('product_stor_id', '=', $product_store)->decrement('quantity', $final);

                    }
                    $data = [
                        'buy_factor_id' => $id,
                        'product_id' => $request->product_id[$item],
                        'buy_price' => $request->product_bprice[$item],
                        'quantity' => $request->product_quantity[$item],
                    ];
                    \DB::table('buy_product')->where('buy_product_id', '=', $buy_product_id)->where('buy_factor_id', '=', $id)->update($data);

                    Session::put('msg_status', true);
                }
                return array(
                    'content' => 'content',
                    'url' => route('buy_factor.list')
                );


            }

        }
    }

    public function create(Request $request)
    {
        $cnames = Company::get()->pluck('company_id', 'company_name');


        $currencies = Currency::all()->where("status", "0");

        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $stack = Store::get()->pluck('store_id', 'store_name');
        } else {
            $stack = Store::where('agency_id', '=', \Auth::user()->agency_id)->get()->pluck('store_id', 'store_name');
        }
        if ($request->isMethod('get'))

            return view('buy_factor.form', compact('cnames', 'stack', 'currencies'))->with(['panel_title' => 'ایجاد فکتور جدید']);
        else {

            $validator = Validator::make(Input::all(), [

                'factor_number' => 'required:unique',
                'total_payment' => 'required',
                'current_payment' => 'required',
                'company_id' => 'required',
                'store_id' => 'required',
                'factor_date' => 'required',

            ]);
            if ($validator->fails()) {


                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );

            }

            $buy_factor = new BuyFactor();
            $buy_factor->company_id = Input::get('company_id');
            $buy_factor->store_id = Input::get('store_id');
            $buy_factor->factor_code = Input::get('factor_number');
            $buy_factor->buy_date = Input::get('factor_date');
            $buy_factor->discount = Input::get('discount');
            $buy_factor->total_payment = Input::get('total_payment');
            $buy_factor->current_payment = Input::get('current_payment');
            $buy_factor->user_id = 1;
            $buy_factor->status = 0;
            $buy_factor->save();

            Session::put('msg_status', true);
            return array(
                'content' => 'content',
                'url' => route('buy_factor.list'),
            );


        }

    }

    public function update(Request $request, $id)
    {
        $factor = BuyFactor::find($id);

        $bfactores = DB::table('buy_factor')
            ->select('buy_factor_id', 'factor_code', 'total_payment')
            ->where('status', '!=', 1)
            ->orderByDesc('buy_factor_id')->get();

//        $Buy_product = DB::table("buy_product")
        $currencies = Currency::all()->where("status", "0");

        $Buy_product = DB::table('buy_product')
            ->join('product', 'product.product_id', '=', 'buy_product.product_id')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->where("buy_product.status", "=", "0")
            ->where('buy_factor_id', $id)
            ->distinct("buy_product_id")
            ->get();


        $companyes = DB::table("company")->where("status", "0")->get();

        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $stores = Store::all()->where('status', '!=', 1);
        } else {
            $stores = Store::where('status', '!=', 1)->where('agency_id', '=', \Auth::user()->agency_id)->get();
        }

        if ($request->isMethod('get')) {

            return view('buy_factor.form', compact('factor', "companyes", "Buy_product", "bfactores", "stores", 'currencies'));

        } else {

            DB::beginTransaction();

            try {

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

                // Getting the form data
                $buy_factor = [
                    "company_id" => $request->input('company_name'),
                    "store_id" => $request->input('stack_name'),
                    "factor_code" => $request->input('sale_factor_code'),
                    "buy_date" => $request->input('pr_date'),
                    "discount" => $request->input('discount'),
                    "total_payment" => $request->input('pr_total'),
                    "current_payment" => $request->input('pr_payment'),
                    'currency_main_id' => get_options("mainCurrency")->value('option_value'),
                    'currency_id' => $request->get('currency_id'),
                    'currency_rate' => $request->get('currency_rate'),
                ];

                // Getting the Buy Factor Data
                $prev_factor_db = BuyFactor::find($id);

                $store_id_db = $prev_factor_db->first()->store_id;

                // update customer payment
                $customerPayment = DB::table('customer_payment')->where('sale_factor_id', $id)
                    ->where('table_related', 'buy_factor')->where('type', 'factorRegister');

                $customerPayment->update([
                    'payment_amount' => $buy_factor['current_payment'],
                    'date' => $buy_factor['buy_date'],
                    'currency_id' => $buy_factor['currency_id'],
                    'currency_rate' => $buy_factor['currency_rate'],
                    'currency_main_id' => $buy_factor['currency_main_id']
                ]);

                // change Money Store

                /**  get related money store that before store money of selling products */

                $prere = $prev_factor_db->current_payment;

                /**  get related money store that before store money of selling products */
                $money_current = MoneyStore::find($prev_factor_db->money_store_id);


                $mountToMoneyStoreBefore = $prere * $prev_factor_db->currency_rate;


                $money_current->increment('money_amount', $mountToMoneyStoreBefore);

                $money_current->decrement('money_amount', ($buy_factor['current_payment'] * $buy_factor['currency_rate']));

                // Check if Stack changed or not

                if ($store_id_db == $buy_factor['store_id']) {


                    if (count($request->product_name) > 0) {

                        // get the previous product items
                        $arr = DB::table("buy_product")
                            ->where("buy_factor_id", $id)
                            ->pluck("buy_product_id")->toArray();

                        // delete the product that not in list
                        if (count($arr) > 0) {

                            $id_come = $request->product_store_id;
                            $def = array_diff($arr, $id_come);

                            // find the differnt of product already and new form submited
                            if (count($def) > 0) {

                                foreach ($def as $d) {

                                    $del = DB::table("buy_product")->where("buy_product_id", $d)
                                        ->select("product_id", "quantity")->first();
                                    $pr = DB::table("product_store")->where("store_id", $store_id_db)
                                        ->where("product_id", $del->product_id);

                                    $pr->update(["quantity" => ($pr->value("quantity") - $del->quantity)]);
                                    DB::table("buy_product")->where("buy_product_id", $d)->delete();

                                }
                            }
                        }


                        // update the products

                        foreach ($request->product_name as $item => $value) {

                            // get the quantity of product
                            $product_quantity = $request->product_quantity[$item];

                            // get Store Ids
                            $proStore = \DB::table('product_store')
                                ->select('store_id')
                                ->where('store_id', '=', $buy_factor['store_id'])
                                ->where('product_id', '=', $request->product_id[$item])
                                ->where('buy_price', '=',  $request->product_bprice[$item])
                                ->get();

                            if (isset($request->product_store_id[$item])) {

                                $product_factor_id = $request->product_store_id[$item];

                                $update_factor = DB::table("buy_product")->where("buy_product_id", $product_factor_id)->first();

                                $current_qu = $update_factor->quantity - $product_quantity;


                                $proStorQuen = \DB::table('product_store')
                                    ->select('store_id')
                                    ->where('store_id', '=', $buy_factor['store_id'])
                                    ->where('product_id', '=', $request->product_id[$item])
                                    ->where('buy_price', '=',  $request->product_bprice[$item]);

                                if ($current_qu < 0) {


                                    $proStorQuen->increment('quantity', abs($current_qu));

                                } else {

                                    $proStorQuen->decrement('quantity', abs($current_qu));
                                }

                                $buyProduct = BuyProduct::find($product_factor_id);
                                $buyProduct->buy_factor_id = $id;
                                $buyProduct->product_id = $request->product_id[$item];
                                $buyProduct->buy_price = $request->product_bprice[$item];
                                $buyProduct->quantity = $request->product_quantity[$item];
                                $buyProduct->product_total = $request->product_total[$item];
                                $buyProduct->save();

                            } else {


                                $product_current = Product::find( $request->product_id[$item]);
                                if (count($proStore) == 0) {


                                    $productStore = new Product_Store();
                                    $productStore->store_id = $buy_factor['store_id'];
                                    $productStore->product_id = $request->product_id[$item];
                                    $productStore->buy_price= $request->product_bprice[$item] ;
                                    $productStore->quantity = $product_quantity;
                                    $productStore->save();


                                } else {
                                    \DB::table('product_store')
                                        ->select('store_id')
                                        ->where('store_id', '=', $buy_factor['store_id'])
                                        ->where('product_id', '=', $request->product_id[$item])
                                        ->where('buy_price', '=',  $request->product_bprice[$item])
                                        ->increment('quantity', $product_quantity);
                                }


                                $buyProduct = new BuyProduct();
                                $buyProduct->buy_factor_id = $id;
                                $buyProduct->product_id = $request->product_id[$item];
                                $buyProduct->buy_price = $request->product_bprice[$item];
                                $buyProduct->quantity = $request->product_quantity[$item];
                                $buyProduct->exchange_unit_id = $request->product_unit_exchange[$item];
                                $buyProduct->main_unit_id = $product_current->unit_id;
                                $buyProduct->product_total = $request->product_total[$item];
                                $buyProduct->save();


                            }


                        }


                    }


                } else {


                    $preproduct = DB::table("buy_product")
                        ->where("buy_factor_id", $id);

                    $prev_product = $preproduct->get();

                    foreach ($prev_product as $prev) {

                        $product_store = DB::table("product_store")
                            ->where("store_id", $prev_factor_db->value("store_id"))
                            ->where("product_id", $prev->product_id);
                        $product_store->update([
                            "quantity" => $product_store->value("quantity") - $prev->quantity,
                        ]);

                    }

                    $preproduct->delete();


                    foreach ($request->product_name as $item => $value) {

                        $store_id = Input::get('stack_name');


                        $product_quantity = $request->product_quantity[$item];


                        $proStore = \DB::table('product_store')
                            ->select('store_id')
                            ->where('store_id', '=', $store_id)
                            ->where('product_id', '=', $request->product_id[$item])->get();


                        if (count($proStore) == 0) {


                            $productStore = new Product_Store();
                            $productStore->store_id = $store_id;
                            $productStore->product_id = $request->product_id[$item];
                            $productStore->quantity = $product_quantity;
                            $productStore->save();


                        } else {
                            \DB::table('product_store')
                                ->select('store_id')
                                ->where('store_id', '=', $store_id)
                                ->where('product_id', '=', $request->product_id[$item])
                                ->increment('quantity', $product_quantity);
                        }


                        $buyProduct = new BuyProduct();
                        $buyProduct->buy_factor_id = $id;
                        $buyProduct->product_id = $request->product_id[$item];
                        $buyProduct->buy_price = $request->product_bprice[$item];
                        $buyProduct->quantity = $request->product_quantity[$item];
                        $buyProduct->product_total = $request->product_total[$item];
                        $buyProduct->save();


                    }


                }


                DB::table('buy_factor')->where('buy_factor_id', $id)->update($buy_factor);


                DB::commit();

                return array(

                    'content' => 'content',
                    'url' => route('buy_factor.list')
                );

            } catch (\Exception $ex) {

                DB::rollBack();
                return array(
                    'fail' => true,
                    'errors' => ['انجام نشد']
                );
            }


        }


    }

    public function delete($id)
    {

        $buy_product = DB::table('buy_product')->select('buy_product_id')
            ->where('buy_factor_id', $id)->get()->toArray();
        $data = array();
        foreach ($buy_product as $key => $value) {
            $data[$key] = $value->buy_product_id;


        }

        foreach ($data as $key => $value) {

            DB::table('buy_product')->where('buy_product_id', $value)->update(['status' => 1]);
        }


        $factor = BuyFactor::find($id);
        $factor->status = 1;
        $factor->save();
        return redirect()->route('buy_factor.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');


    }

    public function searchFactorForPay($id)
    {
        $factorPayment = BuyFactor::find($id);
        return response()->json($factorPayment);

    }

    public function searchFactorForCustomerPay(Request $request, $id = null)
    {

        if (isset($id) && $id > 0) {
            $factorPayment = DB::table("sale_factor")
                ->join("currency", 'currency.currency_id', 'sale_factor.currency_id')
                ->join("customer", 'customer.customer_id', 'sale_factor.customer_id')
                ->where("customer_code", "=", $request->input("customer_code"))
                ->where("total_price", ">", DB::raw("(discount + recieption_price )"))
                ->where("sale_factor.status", "0")
                ->paginate($id);
        } else {
            $factorPayment = DB::table("sale_factor")
                ->join("currency", 'currency.currency_id', 'sale_factor.currency_id')
                ->join("customer", 'customer.customer_id', 'sale_factor.customer_id')
                ->where("customer_code", "=", $request->input("customer_code"))
                ->where("total_price", ">", DB::raw("(discount + recieption_price )"))
                ->where("sale_factor.status", "0")
                ->paginate(5);
        };
        $totalpayment = 0;
        $remind = 0;
//
        if (count($factorPayment) > 0) {


            foreach ($factorPayment as $bFact) {


                $totalpayment += $bFact->total_price;
                $remind += ($bFact->total_price - ($bFact->recieption_price + $bFact->discount));
            }
        }


        return \Response::JSON(array(
            'data' => $factorPayment,
            'totalpayment' => $totalpayment,
            "remind" => $remind,
            "pagination" => (string)$factorPayment->links(),
        ));

    }

    public function buyFactorPayment(Request $request)
    {
        $id = Input::get('factor_id');
        $factorPayment = BuyFactor::find($id);

        $validator = Validator::make(Input::all(), [
                'payment_amount' => 'required',
                "currency_rate" => "required",
                "currency_id" => "required",
                "pr_date" => "required",
            ]
        );
        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        }

        // Add to Customer Payment

        DB::beginTransaction();

        try {


            $customerPayment = new CustomerPayment();
            $customerPayment->payment_amount = Input::get('payment_amount');
            $customerPayment->sale_factor_id = $id;
            $customerPayment->date = Input::get('pr_date');
            $customerPayment->type = 'factorPayment';
            $customerPayment->table_related = 'buy_factor';

            $customerPayment->currency_id = Input::get('currency_id');
            $customerPayment->currency_rate = Input::get('currency_rate');
            $customerPayment->currency_main_id = $factorPayment->currency_main_id;

            $customerPayment->save();

            $currentPayment = ($factorPayment->current_payment + Input::get('payment_amount'));


            $factorPayment->current_payment = $currentPayment;
            $factorPayment->save();


            $currentMoney = Input::get('payment_amount') * Input::get('currency_rate');

            // Update Money Store Table
            $MoneyStore = MoneyStore::find($factorPayment->money_store_id);

            $MoneyStore->increment('money_amount', $currentMoney);


            DB::commit();
            return array(
                'content' => 'content',
                'url' => route('buy_product.report')
            );


        } catch (\Exception $e) {
            DB::rollBack();
            return array(
                'fail' => true,
                'errors' => ["انجام نشد"]
            );
        }


    }


    public function report()
    {
        $companies = Company::all()->where('status', '!=', 1);
        return view('buy_factor.report', compact('companies'))->with('panel_title', 'گزارش فکتورهای خریدشده');
    }


    public function report_data(Request $request, $id = null)
    {



        $total_borrow = '';
        if ($request->ajax()) {

            $output = '';
            $data = '';
            $total_borrow = 0.0;
            $reason = $request->get('company_name');
            $type = $request->get('type');
            $y = $request->get('year');


            // dd($request->input());
            // reason_pay
            if ($reason === 'all') {

                if ($type === 'day') {




                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {


                        if (isset($id) && $id > 0) {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('buy_date', '=', date('Y-m-d'))
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate($id);
                        } else {




                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('buy_date', '=', date('Y-m-d'))
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate(4);
//                            dd($data);

                        }

                        $total_borrow = DB::table('buy_factor')
                            ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                            ->where('buy_date', date('Y-m-d'))->first();


                    } else {
                        if (isset($id) && $id = null) {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_date', '=', date('Y-m-d'))
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate($id);
                        } else {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_date', '=', date('Y-m-d'))
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate(5);
                        }

                        $total_borrow = DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('buy_date', date('Y-m-d'))->first();
                    }


                }
                elseif ($type === 'week') {


                    $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                    $endOfWeek =  \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');



                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate($id);
                        } else {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate(5);


                        }

                        $total_borrow = DB::table('buy_factor')
                            ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                            ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                            ->first();
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate($id);
                        } else {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate(5);
                        }

                        $total_borrow = DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                            ->first();
                    }


                }
                elseif ($type === 'month') {
                    $get_month = $request->get('month_r');
                    if (ctype_digit($get_month)) {
                        $jyear = date('Y');
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
                        $end_day = '30';
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
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);

                            }

                            $total_borrow = DB::table('buy_factor')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->whereBetween('buy_date', [$start_date, $end_date])->first();

                        } else {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);
                            }

                            $total_borrow = DB::table('buy_factor')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$start_date, $end_date])->first();
                        }

                    }
                }
                elseif ($type === 'year') {


                    $getyear = $request->get('year_r');
                    $yaer_date = explode('-', $getyear);
                    $final_year = $yaer_date[0];
                    $startfrom = $final_year . '-01-01';
                    $end = $final_year . '-12-30';


                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {


                            $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->whereBetween('buy_date', [$startfrom, $end])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate($id);

                        } else {


                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->whereBetween('buy_date', [$startfrom, $end])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate(5);


                        }

                        $total_borrow = DB::table('buy_factor')
                            ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                            ->whereBetween('buy_date', [$startfrom, $end])->first();

                    } else {
                        if (isset($id) && $id > 0) {
                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$startfrom, $end])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate($id);
                        } else {


                            $data = DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$startfrom, $end])
                                ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                ->paginate(5);


                        }

                        $total_borrow = DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$startfrom, $end])->first();
                    }


                }
                elseif ($type === 'bt_date') {

                    if ($request->get('year_r') != '') {
                        $start_date = $request->get('year_r');
                        $end_date = $request->get('end-year');

                        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {

                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount',
                                        DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {

                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount',
                                        DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate();

                            }
                            $total_borrow = DB::table('buy_factor')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->first();


                        } else {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount',
                                        DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount',
                                        DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);
                            }

                            $total_borrow = DB::table('buy_factor')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$start_date, $end_date])->first();
                        }
                    }

                }
            }
            else {
                if (ctype_digit($reason)) {
                    if ($type === 'day') {


                        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->where('buy_date', '=', date("Y-m-d"))
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->where('buy_date', '=',  date("Y-m-d"))
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);
                            }
                            $total_borrow = DB::table('buy_factor')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('buy_factor.company_id', '=', $reason)
                                ->where('buy_date', '=',  date("Y-m-d"))
                                ->first();
                        } else {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->where('buy_date', '=',  date("Y-m-d"))
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->where('buy_date', '=',  date("Y-m-d"))
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);
                            }
                            $total_borrow = DB::table('buy_factor')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_factor.company_id', '=', $reason)
                                ->where('buy_date', '=',  date("Y-m-d"))
                                ->first();
                        }


                    } elseif ($type === 'week') {




                        $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                        $endOfWeek =  \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');

                        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date',  [$startOfWeek, $endOfWeek])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);
                            }

                            $total_borrow = DB::table('buy_factor')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('buy_factor.company_id', '=', $reason)
                                ->whereBetween('buy_date',  [$startOfWeek, $endOfWeek])->first();
                        } else {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->whereBetween('buy_date',  [$startOfWeek, $endOfWeek])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {
                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date',  [$startOfWeek, $endOfWeek])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);
                            }
                            $total_borrow = DB::table('buy_factor')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_factor.company_id', '=', $reason)
                                ->whereBetween('buy_date', [$startOfWeek, $endOfWeek])->first();
                        }

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
                            $end_day = '30';
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
                                    $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate($id);
                                } else {
                                    $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate(5);
                                }

                                $total_borrow = DB::table('buy_factor')
                                    ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$start_date, $end_date])->first();
                            } else {
                                if (isset($id) && $id > 0) {
                                    $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate($id);
                                } else {
                                    $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate(5);
                                }
                                $total_borrow = DB::table('buy_factor')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$start_date, $end_date])->first();
                            }
                        }


                    } elseif ($type === 'year') {

                        $getyear = $request->get('year_r');
                        $yaer_date = explode('-', $getyear);
                        $final_year = $yaer_date[0];
                        $startfrom = $final_year . '-01-01';
                        $end = $final_year . '-12-30';

                        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                            if (isset($id) && $id > 0) {
                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$startfrom, $end])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);
                            } else {

                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$startfrom, $end])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);

                            }
                            $total_borrow = DB::table('buy_factor')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('buy_factor.company_id', '=', $reason)
                                ->whereBetween('buy_date', [$startfrom, $end])->first();
                        } else {
                            if (isset($id) && $id) {


                                $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$startfrom, $end])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate($id);


                            } else {


                                $data = DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                    ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))->where('buy_factor.company_id', '=', $reason)
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$startfrom, $end])
                                    ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                    ->paginate(5);


                            }
                            $total_borrow = DB::table('buy_factor')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_factor.company_id', '=', $reason)
                                ->whereBetween('buy_date', [$startfrom, $end])->first();
                        }

                    } elseif ($type === 'bt_date') {

                        if ($request->get('start_date') != '') {
                            $start_date = $request->get('start_date');
                            $end_date = $request->get('end_date');

                            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                                if (isset($id) && $id > 0) {

                                    $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate($id);

                                } else {

                                    $data = DB::table('buy_factor')->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate(5);

                                }

                                $total_borrow = DB::table('buy_factor')
                                    ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$start_date, $end_date])->first();
                            }
                            else {
                                if (isset($id) && $id > 0) {
                                    $data = DB::table('buy_factor')
                                        ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate($id);
                                } else {
                                    $data = DB::table('buy_factor')
                                        ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                        ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                        ->join('currency', 'buy_factor.currency_id', '=', 'currency.currency_id')
                                        ->select('currency_name', 'company_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount', DB::raw('(total_payment-(current_payment+discount)) as baqi'))
                                        ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                        ->where('buy_factor.company_id', '=', $reason)
                                        ->whereBetween('buy_date', [$start_date, $end_date])
                                        ->groupBy('company_name', 'currency_name', 'factor_code', 'buy_date', 'total_payment', 'current_payment', 'discount')
                                        ->paginate(5);
                                }

                                $total_borrow = DB::table('buy_factor')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->selectRaw('sum(total_payment-(current_payment+discount)) as total_borrow')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $reason)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->first();
                            }
                        }

                    }

                }
            }


            return \Response::JSON(array(
                "data" => $data,
                "total_borrow" => $total_borrow->total_borrow,
                "pagination" => (string)$data->links(),
            ));
        }

    }


}
