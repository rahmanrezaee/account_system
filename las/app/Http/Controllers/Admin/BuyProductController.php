<?php

namespace App\Http\Controllers\Admin;

use App\Models\BuyFactor;
use App\Models\BuyProduct;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\MoneyStore;
use App\Models\Product;
use App\Models\Product_Store;
use App\Models\Store;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;

class BuyProductController extends Controller
{
    public function index()
    {
        return view('buy_factor.index');
    }

    public function create(Request $request)
    {
        $bfactores = DB::table('buy_factor')
            ->select('buy_factor_id', 'factor_code', 'total_payment')
            ->where('status', '!=', 1)
            ->orderByDesc('buy_factor_id')->get();

        $companyes = Company::where("status", '=', 0)->get();
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $stores = DB::table("store")->where("status", '=', 0)->get();
        } else {
            $stores = DB::table("store")
                ->where('agency_id', '=', Auth::user()->agency_id)
                ->where("status", "=", 0)
                ->get();
        }
        $currencies = Currency::where("status", "0")->get();

        $lastFactorCode = DB::table("buy_factor")->latest("factor_code")->value("factor_code");


        if ($request->isMethod('get')) {

            return view('buy_product.form', compact('bfactores', "companyes", "stores", 'currencies', 'lastFactorCode'));

        } else {


            $validator = Validator::make(Input::all(), [
                'sale_factor_code' => 'required',
                'company_name' => 'required',
                'stack_name' => 'required',
                'pr_date' => 'required',
                "product_quantity" => 'required',
                "product_total" => 'required',
                "currency_id" => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            DB::beginTransaction();
            try {


                $buy_factor = new BuyFactor();
                $buy_factor->company_id = Input::get('company_name');
                $buy_factor->store_id = Input::get('stack_name');
                $buy_factor->factor_code = Input::get('sale_factor_code');
                $buy_factor->buy_date = Input::get('pr_date');
                $buy_factor->discount = Input::get('discount');
                $buy_factor->total_payment = Input::get('pr_total');
                $buy_factor->current_payment = Input::get('pr_payment');
                $buy_factor->currency_id = Input::get('currency_id');
                $buy_factor->currency_main_id = get_options("mainCurrency")->value('option_value');
                $buy_factor->currency_rate = Input::get('currency_rate');
                $buy_factor->money_store_id = get_options("mainMoneyStore")->value('option_value');
                $buy_factor->user_id = 1;
                $buy_factor->status = 0;
                $buy_factor->save();


                // Update Money Store Table
                $MoneyStore = MoneyStore::find($buy_factor->money_store_id);


                $mountToMoneyStore = $buy_factor->current_payment * $buy_factor->currency_rate;
                $MoneyStore->decrement('money_amount', $mountToMoneyStore);

                // Add to Customer Payment
                $customerPayment = new CustomerPayment();
                $customerPayment->payment_amount = $buy_factor->current_payment;
                $customerPayment->sale_factor_id = $buy_factor->buy_factor_id;
                $customerPayment->date = Input::get('pr_date');
                $customerPayment->type = 'factorRegister';
                $customerPayment->table_related = 'buy_factor';

                $customerPayment->currency_id = $buy_factor->currency_id;
                $customerPayment->currency_rate = $buy_factor->currency_rate;
                $customerPayment->currency_main_id = $buy_factor->currency_main_id;

                $customerPayment->save();


                if (count($request->product_name) > 0) {

                    foreach ($request->product_name as $item => $value) {

                        $store_id = Input::get('stack_name');


                        $product_current = Product::find( $request->product_id[$item]);
                        $product_quantity = $request->product_quantity[$item];


                        //convert to Exchange Unit
                        if($request->product_unit_exchange[$item] != $product_current->unit_id){


                            $exchange_unit = DB::table("unit_exchange")
                                ->where("main_unit_id",$product_current->unit_id)
                                ->where("relate_unit_id",$request->product_unit_exchange[$item])
                                ->first();

                            $product_quantity = $product_quantity * ( $exchange_unit->main_quentity / $exchange_unit->quentity );

                        }

                        $proStore = \DB::table('product_store')
                            ->select('store_id')
                            ->where('store_id', '=', $store_id)
                            ->where('buy_price', '=',  $request->product_bprice[$item])
                            ->where('product_id', '=', $request->product_id[$item])
                            ->get();

                        if (count($proStore) <= 0) {
                            $productStore = new Product_Store();
                            $productStore->store_id = $store_id;
                            $productStore->product_id = $request->product_id[$item];
                            $productStore->buy_price = $request->product_bprice[$item];
                            $productStore->quantity = $product_quantity;
                            $productStore->save();
                        } else {
                            \DB::table('product_store')
                                ->select('store_id')
                                ->where('store_id', '=', $store_id)
                                ->where('product_id', '=', $request->product_id[$item])
                                ->where('buy_price', '=',  $request->product_bprice[$item])
                                ->increment('quantity', $product_quantity);
                        }

                        $buyProduct = new BuyProduct();
                        $buyProduct->buy_factor_id = $buy_factor->buy_factor_id;
                        $buyProduct->product_id = $request->product_id[$item];
                        $buyProduct->buy_price = $request->product_bprice[$item];
                        $buyProduct->buy_price = $request->product_bprice[$item];
                        $buyProduct->exchange_unit_id = $request->product_unit_exchange[$item];
                        $buyProduct->main_unit_id = $product_current->unit_id;
                        $buyProduct->quantity = $request->product_quantity[$item];
                        $buyProduct->product_total = $request->product_total[$item];

                        $buyProduct->save();
                        Session::put('msg_status', true);

                    }


                    DB::commit();
                    return array(

                        'content' => 'content',
                        'url' => route('buy_product.create')
                    );

                }


            } catch (PDOException $exception) {
                return array(
                    'fail' => true,
                    'errors' => ["خطا" => $exception->errorInfo[2]]
                );
                DB::rollBack();
            }


        }
    }

    public function delete($id)
    {


    }

    public function searchResponse(Request $request)
    {
        $store_id = \Session::get('store_id');
        $name = $request->get('name');

        $fieldName = $request->get('fieldName');
        if ($request->get('fieldName') === 'pquantity') {
            $fieldName = 'product_name';
        }
        $users = DB::table('product')
            ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
            ->select('product.product_name', 'product.product_id', 'product.product_code', 'product_unit.unit_name', 'product.unit_id')
            ->where("$fieldName", 'LIKE', '%' . $name . '%')
            ->get();
        return $users;
    }

    public function fetchData(Request $request)
    {
        $buy_factor_id = $request->get('buy_factor_id');
        $buyFactor = BuyFactor::find($buy_factor_id);
        $company_name = $buyFactor->company->company_name;
        $store_name = $buyFactor->stack->store_name;
        $total_payment = $buyFactor->total_payment;
        $factor_date = $buyFactor->buy_date;
        Session::put('store_id', $buyFactor->store_id);

        $data = array(
            'company_name' => $company_name,
            'store_name' => $store_name,
            'factor_date' => $factor_date,
            'total_payment' => $total_payment,
        );
        return json_encode($data);

    }

    public function searchFactorPaymentByCompany(Request $request, $id = null)
    {
        $company_id = $request->get('company_id');

        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {


                $buyFactores = \DB::table('buy_factor')
                    ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                    ->select('currency_name', DB::raw('current_payment + discount as remind'),'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'buy_factor.company_id', 'total_payment', 'current_payment', 'discount')
                    ->where('buy_factor.status', '!=', 1)
                    ->where('buy_factor.company_id', '=', $company_id)
                    ->where('total_payment', '>', DB::raw('(current_payment+discount)'))

                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                    ->paginate($id);
            } else {


                $buyFactores = \DB::table('buy_factor')
                    ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                    ->select('currency_name', DB::raw('current_payment + discount as remind'),'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'buy_factor.company_id', 'total_payment', 'current_payment', 'discount')
                    ->where('buy_factor.status', '!=', 1)
                    ->where('buy_factor.company_id', '=', $company_id)
                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                    ->where('total_payment', '>', DB::raw('(current_payment+discount)'))

                    ->paginate(5);
            }


        } else {
            if (isset($id) && $id > 0) {


                $buyFactores = \DB::table('buy_factor')
                     ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                    ->select('currency_name', DB::raw('current_payment + discount as remind'),'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'buy_factor.company_id', 'total_payment', 'current_payment', 'discount')
                    ->where('buy_factor.status', '!=', 1)
                    ->where('buy_factor.company_id', '=', $company_id)
                    ->where('total_payment', '>', 'current_payment + discount')
                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                    ->where('total_payment', '>', DB::raw('(current_payment+discount)'))

                    ->paginate($id);
            } else {

                $buyFactores = \DB::table('buy_factor')
                    ->select('currency_name', DB::raw('current_payment + discount as remind'),'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'buy_factor.company_id', 'total_payment', 'current_payment', 'discount')
                    ->where('buy_factor.status', '!=', 1)
                    ->where('buy_factor.company_id', '=', $company_id)
                    ->where('total_payment', '>', 'current_payment + discount')
                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                    ->where('total_payment', '>', DB::raw('(current_payment+discount)'))

                    ->paginate(5);
            }

        }

        return \Response::JSON(array(
            "data" => $buyFactores,
            "pagination" => (string)$buyFactores->links(),
        ));

    }

    public function report()
    {
        $company = Company::all()->where('status', '!=', 1);
        $currencies = Currency::where("status", "0")->get();


        return view('buy_product.report', compact('company', 'currencies'))->with('panel_title', 'نمایش بدهی از شرکت های فروشنده');
    }

    public function report_data(Request $request)
    {
        if ($request->ajax()) {
            $total = 0;
            $output = '';
            $company = $request->get('company');
            $type = $request->get('type');
            $y = $request->get('year');

            // reason_pay
            if ($company === 'all') {

                $data = \DB::table('buy_factor')->where('status', '!=', 1)->get();

                if ($type === 'day') {
                    $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                    $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                    $jday = Jalalian::fromCarbon(Carbon::now())->getDay();
                    $jdate = '';
                    if ($jmonth < 10 && $jday > 9) {
                        $jdate = $jyear . '-0' . $jmonth . '-' . $jday;
                    } elseif ($jday < 10 && $jmonth > 9) {
                        $jdate = $jyear . '-' . $jmonth . '-0' . $jday;

                    } elseif ($jmonth < 10 && $jday < 10) {
                        $jdate = $jyear . '-0' . $jmonth . '-0' . $jday;
                    } else {
                        $jdate = $jyear . '-' . $jmonth . '-' . $jday;
                    }

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        $data = \DB::table('buy_factor')
                            ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                            ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                            ->where('buy_date', '=', $jdate)
                            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                            ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                            ->paginate(5);
                    } else {
                        $data = \DB::table('buy_factor')
                            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                            ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('buy_date', '=', $jdate)
                            ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                            ->paginate(5);
                    }

                } elseif ($type === 'week') {
                    $year = Jalalian::fromCarbon(Carbon::now())->getYear();
                    $month = Jalalian::fromCarbon(Carbon::now())->getMonth();
                    $day = Jalalian::fromCarbon(Carbon::now())->getDay();
                    $date = '';
                    if ($month < 10 && $day > 9) {
                        $date = $year . '-0' . $month . '-' . $day;
                    } elseif ($day < 10 && $month > 9) {
                        $date = $year . '-' . $month . '-0' . $day;

                    } elseif ($month < 10 && $day < 10) {
                        $date = $year . '-0' . $month . '-0' . $day;
                    } else {
                        $date = $year . '-' . $month . '-' . $day;
                    }


                    $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                    $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                    $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                    $dayofweek = Jalalian::fromCarbon(Carbon::now())->getDayOfWeek();

                    switch ($dayofweek) {
                        case 0:
                            $jday = $jday;
                            break;
                        case 1:
                            $jday = $jday - 1;
                            break;
                        case 2:
                            $jday = $jday - 2;
                            break;
                        case 3:
                            $jday = $jday - 3;
                            break;
                        case 4:
                            $jday = $jday - 4;
                            break;
                        case 5:
                            $jday = $jday - 5;
                            break;
                        case 6:
                            $jday = $jday - 6;
                            break;

                    }
                    $jdate = '';
                    if ($jmonth < 10 && $jday > 9) {
                        $jdate = $jyear . '-0' . $jmonth . '-' . $jday;
                    } elseif ($jday < 10 && $jmonth > 9) {
                        $jdate = $jyear . '-' . $jmonth . '-0' . $jday;

                    } elseif ($jmonth < 10 && $jday < 10) {
                        $jdate = $jyear . '-0' . $jmonth . '-0' . $jday;
                    } else {
                        $jdate = $jyear . '-' . $jmonth . '-' . $jday;
                    }

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        $data = \DB::table('buy_factor')
                            ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                            ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name',
                                \DB::raw('count(buy_product.product_id) as total_product ,current_payment,total_payment', 'total_payment', 'current_payment'))
                            ->whereBetween('buy_date', [$jdate, $date])
                            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                            ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                            ->paginate(5);
                    } else {
                        $data = \DB::table('buy_factor')
                            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                            ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name',
                                \DB::raw('count(buy_product.product_id) as total_product ,current_payment,total_payment', 'total_payment', 'current_payment'))
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$jdate, $date])
                            ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                            ->paginate(5);
                    }

                } elseif ($type === 'month') {
                    $get_month = $request->get('month_r');
                    if (ctype_digit($get_month)) {
                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
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

                        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                            $data = \DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment')
                                ->paginate(5);
                        } else {
                            $data = \DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment')
                                ->paginate(5);
                        }

                    }
                } elseif ($type === 'year') {
                    $getyear = $request->get('year_r');
                    $yaer_date = explode('/', $getyear);
                    $final_year = $yaer_date[0];
                    $startfrom = $final_year . '-01-01';
                    $end = $final_year . '-12-31';

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        $data = \DB::table('buy_factor')
                            ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                            ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', \DB::raw('count(buy_product.product_id) as total_product'))
                            ->whereBetween('buy_date', [$startfrom, $end])
                            ->join('buy_product', 'buy_factor.buy_factor_id', '=', 'buy_product.buy_factor_id')
                            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                            ->groupBy('factor_code', 'buy_date', 'company_name')
                            ->paginate(5);
                    } else {
                        $data = \DB::table('buy_factor')
                            ->join('buy_product', 'buy_factor.buy_factor_id', '=', 'buy_product.buy_factor_id')
                            ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                            ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', \DB::raw('count(buy_product.product_id) as total_product'))
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$startfrom, $end])
                            ->groupBy('factor_code', 'buy_date', 'company_name')
                            ->paginate(5);
                    }

                } elseif ($type === 'bt_date') {
                    if ($request->get('start_date') != '') {
                        $start_date = $request->get('start_date');
                        $end_date = $request->get('end_date');

                        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                            $data = \DB::table('buy_factor')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                                ->paginate(5);
                        } else {
                            $data = \DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                                ->paginate(5);
                        }
                    }

                }
            } else {
                if (ctype_digit($company)) {
                    if ($type === 'day') {
                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                        $jday = Jalalian::fromCarbon(Carbon::now())->getDay();
                        $jdate = '';
                        if ($jmonth < 10 && $jday > 9) {
                            $jdate = $jyear . '-0' . $jmonth . '-' . $jday;
                        } elseif ($jday < 10 && $jmonth > 9) {
                            $jdate = $jyear . '-' . $jmonth . '-0' . $jday;

                        } elseif ($jmonth < 10 && $jday < 10) {
                            $jdate = $jyear . '-0' . $jmonth . '-0' . $jday;
                        } else {
                            $jdate = $jyear . '-' . $jmonth . '-' . $jday;
                        }

                        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                            $data = \DB::table('buy_factor')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('buy_factor.company_id', '=', $company)
                                ->where('buy_date', '=', $jdate)
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                                ->paginate(5);
                        } else {
                            $data = \DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_factor.company_id', '=', $company)
                                ->where('buy_date', '=', $jdate)
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                                ->paginate(5);
                        }

                    } elseif ($type === 'week') {
                        $year = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $month = Jalalian::fromCarbon(Carbon::now())->getMonth();
                        $day = Jalalian::fromCarbon(Carbon::now())->getDay();
                        $date = '';
                        if ($month < 10 && $day > 9) {
                            $date = $year . '-0' . $month . '-' . $day;
                        } elseif ($day < 10 && $month > 9) {
                            $date = $year . '-' . $month . '-0' . $day;

                        } elseif ($month < 10 && $day < 10) {
                            $date = $year . '-0' . $month . '-0' . $day;
                        } else {
                            $date = $year . '-' . $month . '-' . $day;
                        }


                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                        $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                        $dayofweek = Jalalian::fromCarbon(Carbon::now())->getDayOfWeek();

                        switch ($dayofweek) {
                            case 0:
                                $jday = $jday;
                                break;
                            case 1:
                                $jday = $jday - 1;
                                break;
                            case 2:
                                $jday = $jday - 2;
                                break;
                            case 3:
                                $jday = $jday - 3;
                                break;
                            case 4:
                                $jday = $jday - 4;
                                break;
                            case 5:
                                $jday = $jday - 5;
                                break;
                            case 6:
                                $jday = $jday - 6;
                                break;

                        }
                        $jdate = '';
                        if ($jmonth < 10 && $jday > 9) {
                            $jdate = $jyear . '-0' . $jmonth . '-' . $jday;
                        } elseif ($jday < 10 && $jmonth > 9) {
                            $jdate = $jyear . '-' . $jmonth . '-0' . $jday;

                        } elseif ($jmonth < 10 && $jday < 10) {
                            $jdate = $jyear . '-0' . $jmonth . '-0' . $jday;
                        } else {
                            $jdate = $jyear . '-' . $jmonth . '-' . $jday;
                        }

                        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                            $data = \DB::table('buy_factor')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('buy_factor.company_id', '=', $company)
                                ->whereBetween('buy_date', [$jdate, $date])
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment', 'buy_factor_id')
                                ->paginate(5);
                        } else {
                            $data = \DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('buy_factor.company_id', '=', $company)
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$jdate, $date])
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment', 'buy_factor_id')
                                ->paginate(5);
                        }

                    } elseif ($type === 'month') {

                        $get_month = $request->get('month_r');
                        if (ctype_digit($get_month)) {
                            $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
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

                            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                                $data = \DB::table('buy_factor')
                                    ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                    ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                    ->where('buy_factor.company_id', '=', $company)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment', 'buy_factor_id')
                                    ->paginate(5);
                            } else {
                                $data = \DB::table('buy_factor')
                                    ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                    ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                    ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                    ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                    ->where('buy_factor.company_id', '=', $company)
                                    ->whereBetween('buy_date', [$start_date, $end_date])
                                    ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment', 'buy_factor_id')
                                    ->paginate(5);
                            }
                        }


                    } elseif ($type === 'year') {

                        $getyear = $request->get('year_r');
                        $yaer_date = explode('/', $getyear);
                        $final_year = $yaer_date[0];
                        $startfrom = $final_year . '-01-01';
                        $end = $final_year . '-12-31';

                        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                            $data = \DB::table('buy_factor')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('buy_factor.company_id', '=', $company)
                                ->whereBetween('buy_date', [$startfrom, $end])
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment', 'buy_factor_id')
                                ->paginate(5);
                        } else {
                            $data = \DB::table('buy_factor')
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->where('buy_factor.company_id', '=', $company)
                                ->whereBetween('buy_date', [$startfrom, $end])
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment', 'buy_factor_id')
                                ->paginate(5);
                        }


                    } elseif ($type === 'bt_date') {
                        if ($request->get('start_date') != '') {
                            $start_date = $request->get('start_date');
                            $end_date = $request->get('end_date');

                            $data = \DB::table('buy_factor')
                                ->join('currency', 'currency.currency_id', 'buy_factor.currency_id')
                                ->select('currency_name', 'buy_factor_id', 'factor_code', 'buy_date', 'company.company_name', 'total_payment', 'current_payment')
                                ->where('buy_factor.company_id', '=', $company)
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->join('company', 'buy_factor.company_id', '=', 'company.company_id')
                                ->groupBy('factor_code', 'buy_date', 'company_name', 'total_payment', 'current_payment')
                                ->paginate(5);
                        }

                    }

                }
            }
            /* Session::push('data',$data);
             Session::push('sum',$sum);*/


            $total_row = $data->count();

            return \Response::JSON(array(
                "data" => $data,
                'total_data' => $total_row,
                "pagination" => (string)$data->links(),
            ));
        }

    }


}

