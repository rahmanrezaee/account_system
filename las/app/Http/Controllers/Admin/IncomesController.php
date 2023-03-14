<?php

namespace App\Http\Controllers\Admin;

use App\Models\Owner;
use App\Models\Product;
use App\Models\Reason_Pay;
use App\Models\Sale_Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Morilog\Jalali\Jalalian;

class IncomesController extends Controller
{
    public function index()
    {
        return view('income.report');
    }

    public function getOwnerPercentage()
    {
        $ownerInfo = \DB::table('owner')->select('full_name', 'percentage')
            ->where('status', '!=', 1)
            ->where('percentage', '>', 0)->get();
        return $ownerInfo;
    }

    public function report_data(Request $request)
    {


        if ($request->ajax()) {
            $finalData = array();

            DB::beginTransaction();

            try{

                $income = 0.0;
                $output = '';
                $type = $request->get('type');

                if ($type == "type-1") {

                    $data = [
                        'sum_of_buy_factor' => 0,
                        'sum_of_salary_payment' => 0,
                        'total_salary' => 0,
                        'total_expense' => 0,
                        'total_car_income' => 0,
                        'total_sale_factor' => 0,
                        "sum_of_benif" => 0
                    ];

                    $finalData = $data;

                }
                //daily
                if ($type == 'day') {
                    $year = Jalalian::fromCarbon(Carbon::now())->getYear();
                    $month = Jalalian::fromCarbon(Carbon::now())->getMonth();
                    $day = Jalalian::fromCarbon(Carbon::now())->getDay();
                    $jdate = '';
                    if ($month < 10 && $day > 9) {
                        $jdate = $year . '-0' . $month . '-' . $day;
                    } elseif ($day < 10 && $month > 9) {
                        $jdate = $year . '-' . $month . '-0' . $day;

                    } elseif ($month < 10 && $day < 10) {
                        $jdate = $year . '-0' . $month . '-0' . $day;
                    } else {
                        $jdate = $year . '-' . $month . '-' . $day;
                    }


                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->where('sale_date', $jdate)
                            ->sum('total_price');

                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->where('payment_date', $jdate)
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->where('pay_date', $jdate)
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->where('buy_date', $jdate)
                            ->sum('total_payment');

                        $sum_of_car_income = DB::table('car_revenue')
                            ->where('date', $jdate)
                            ->sum('revenue_amount');


                        $sum_of_benif = \DB::table('sale_product')
                            ->where('sale_date', $jdate)
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    } else {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('sale_date', $jdate)
                            ->sum('sale_factor.total_price');

                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->join('money_store', 'salary_payment.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('payment_date', $jdate)
                            ->sum('salary_payment.payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->join('money_store', 'expense.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('pay_date', $jdate)
                            ->sum('expense.amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('buy_date', $jdate)
                            ->sum('total_payment');

                        $sum_of_car_income = DB::table('car_revenue')
                            ->where('date', $jdate)
                            ->sum('revenue_amount');

                        $sum_of_benif = \DB::table('sale_product')
                            ->join('sale_factor', 'sale_product.sale_factor_id', '=', 'sale_factor.sale_factor_id')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->where('sale_product.sale_date', $jdate)
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    }

//                $income = ($sum_of_total_payment) - ($sum_of_sale_factor + $sum_of_expense + $sum_of_salary);
                    $data = [
                        'sum_of_buy_factor' => $sum_of_buy_factor,
                        'sum_of_salary_payment' => $sum_of_salary_payment,
                        'total_salary' => $sum_of_salary_payment,
                        'total_car_income' => $sum_of_car_income,
                        'total_expense' => $sum_of_expense,
                        'total_sale_factor' => $sum_of_sale_factor,
                        "sum_of_benif" => ($sum_of_benif + $sum_of_car_income) - ($sum_of_salary_payment + $sum_of_expense)
                    ];


                    $finalData = $data;
                }
                // weekly report
                if ($type == 'week') {
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
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$jdate, $date])
                            ->sum('total_price');
                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->whereBetween('payment_date', [$jdate, $date])
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->whereBetween('pay_date', [$jdate, $date])
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->whereBetween('buy_date', [$jdate, $date])
                            ->sum('total_payment');

                        $sum_of_car_income = \DB::table('car_revenue')
                            ->whereBetween('date', [$jdate, $date])
                            ->sum('revenue_amount');


                        $sum_of_benif = \DB::table('sale_product')
                            ->whereBetween('sale_date', [$jdate, $date])
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    } else {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$jdate, $date])
                            ->sum('total_price');
                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->join('money_store', 'salary_payment.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('payment_date', [$jdate, $date])
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->join('money_store', 'expense.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('pay_date', [$jdate, $date])
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$jdate, $date])
                            ->sum('total_payment');

                        $sum_of_car_income = \DB::table('car_revenue')
                            ->whereBetween('date', [$jdate, $date])
                            ->sum('revenue_amount');


                        $sum_of_benif = \DB::table('sale_product')
                            ->join('sale_factor', 'sale_product.sale_factor_id', '=', 'sale_factor.sale_factor_id')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('sale_product.sale_date', [$jdate, $date])
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    }

//                $income = ($sum_of_total_payment) - ($sum_of_sale_factor + $sum_of_expense + $sum_of_salary);
                    $data = [
                        'sum_of_buy_factor' => $sum_of_buy_factor,
                        'sum_of_salary_payment' => $sum_of_salary_payment,
                        'total_salary' => $sum_of_salary_payment,
                        'total_car_income' => $sum_of_car_income,
                        'total_expense' => $sum_of_expense,
                        'total_sale_factor' => $sum_of_sale_factor,
                        "sum_of_benif" => ($sum_of_benif + $sum_of_car_income) - ($sum_of_salary_payment + $sum_of_expense)
                    ];

                    $finalData = $data;

                }
                // monthly report
                if ($type === 'month') {
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
                            $sum_of_sale_factor = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('total_price');
                            $sum_of_salary_payment = \DB::table('salary_payment')
                                ->whereBetween('payment_date', [$start_date, $end_date])
                                ->sum('payment_amount');

                            $sum_of_expense = \DB::table('expense')
                                ->whereBetween('pay_date', [$start_date, $end_date])
                                ->sum('amount');

                            $sum_of_buy_factor = \DB::table('buy_factor')
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->sum('total_payment');

                            $sum_of_car_income = DB::table('car_revenue')
                                ->whereBetween('date', [$start_date, $end_date])
                                ->sum('revenue_amount');

                            $sum_of_benif = \DB::table('sale_product')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum(DB::raw("(sale_price - buy_price)"));
                        } else {
                            $sum_of_sale_factor = \DB::table('sale_factor')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('total_price');
                            $sum_of_salary_payment = \DB::table('salary_payment')
                                ->join('money_store', 'salary_payment.account_id', '=', 'money_store.store_id')
                                ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('payment_date', [$start_date, $end_date])
                                ->sum('payment_amount');

                            $sum_of_expense = \DB::table('expense')
                                ->join('money_store', 'expense.account_id', '=', 'money_store.store_id')
                                ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('pay_date', [$start_date, $end_date])
                                ->sum('amount');

                            $sum_of_buy_factor = \DB::table('buy_factor')
                                ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('buy_date', [$start_date, $end_date])
                                ->sum('total_payment');

                            $sum_of_car_income = DB::table('car_revenue')
                                ->whereBetween('date', [$start_date, $end_date])
                                ->sum('revenue_amount');

                            $sum_of_benif = \DB::table('sale_product')
                                ->join('sale_factor', 'sale_product.sale_factor_id', '=', 'sale_factor.sale_factor_id')
                                ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                                ->where('store.agency_id', '=', \Auth::user()->agency_id)
                                ->whereBetween('sale_product.sale_date', [$start_date, $end_date])
                                ->sum(DB::raw("(sale_price - buy_price)"));
                        }

//                $income = ($sum_of_total_payment) - ($sum_of_sale_factor + $sum_of_expense + $sum_of_salary);
                        $data = [
                            'sum_of_buy_factor' => $sum_of_buy_factor,
                            'sum_of_salary_payment' => $sum_of_salary_payment,
                            'total_salary' => $sum_of_salary_payment,
                            'total_car_income' => $sum_of_car_income,
                            'total_expense' => $sum_of_expense,
                            'total_sale_factor' => $sum_of_sale_factor,
                            "sum_of_benif" => ($sum_of_benif + $sum_of_car_income) - ($sum_of_salary_payment + $sum_of_expense)

                        ];

                        $finalData = $data;


                    }

                }
                // yearly report
                if ($type === 'year') {

                    $get_year = $request->get('year_r');
                    $yaer_date = explode('/', $get_year);
                    $final_year = $yaer_date[0];
                    $start_date = $final_year . '-01-01';
                    $end_date = $final_year . '-12-30';

                    if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');
                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->whereBetween('payment_date', [$start_date, $end_date])
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->whereBetween('pay_date', [$start_date, $end_date])
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->whereBetween('buy_date', [$start_date, $end_date])
                            ->sum('total_payment');
                        $sum_of_car_income = DB::table('car_revenue')
                            ->whereBetween('date', [$start_date, $end_date])
                            ->sum('revenue_amount');

                        $sum_of_benif = \DB::table('sale_product')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    } else {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');
                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->join('money_store', 'salary_payment.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('payment_date', [$start_date, $end_date])
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->join('money_store', 'expense.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('pay_date', [$start_date, $end_date])
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$start_date, $end_date])
                            ->sum('total_payment');
                        $sum_of_car_income = DB::table('car_revenue')
                            ->whereBetween('date', [$start_date, $end_date])
                            ->sum('revenue_amount');

                        $sum_of_benif = \DB::table('sale_product')
                            ->join('sale_factor', 'sale_product.sale_factor_id', '=', 'sale_factor.sale_factor_id')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('sale_product.sale_date', [$start_date, $end_date])
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    }

//                $income = ($sum_of_total_payment) - ($sum_of_sale_factor + $sum_of_expense + $sum_of_salary);
                    $data = [
                        'sum_of_buy_factor' => $sum_of_buy_factor,
                        'sum_of_salary_payment' => $sum_of_salary_payment,
                        'total_salary' => $sum_of_salary_payment,
                        'total_car_income' => $sum_of_car_income,
                        'total_expense' => $sum_of_expense,
                        'total_sale_factor' => $sum_of_sale_factor,
                        "sum_of_benif" => ($sum_of_benif + $sum_of_car_income) - ($sum_of_salary_payment + $sum_of_expense)
                    ];

                    $finalData = $data;


                }
                // between tow date report
                if ($type === 'bt_date') {

                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');
                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->whereBetween('payment_date', [$start_date, $end_date])
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->whereBetween('pay_date', [$start_date, $end_date])
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->whereBetween('buy_date', [$start_date, $end_date])
                            ->sum('total_payment');

                        $sum_of_car_income = DB::table('car_revenue')
                            ->where('date', [$start_date, $end_date])
                            ->sum('revenue_amount');

                        $sum_of_benif = \DB::table('sale_product')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    } else {
                        $sum_of_sale_factor = \DB::table('sale_factor')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');
                        $sum_of_salary_payment = \DB::table('salary_payment')
                            ->join('money_store', 'salary_payment.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('payment_date', [$start_date, $end_date])
                            ->sum('payment_amount');

                        $sum_of_expense = \DB::table('expense')
                            ->join('money_store', 'expense.account_id', '=', 'money_store.store_id')
                            ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('pay_date', [$start_date, $end_date])
                            ->sum('amount');

                        $sum_of_buy_factor = \DB::table('buy_factor')
                            ->join('store', 'buy_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('buy_date', [$start_date, $end_date])
                            ->sum('total_payment');

                        $sum_of_car_income = DB::table('car_revenue')
                            ->where('date', [$start_date, $end_date])
                            ->sum('revenue_amount');

                        $sum_of_benif = \DB::table('sale_product')
                            ->join('sale_factor', 'sale_product.sale_factor_id', '=', 'sale_factor.sale_factor_id')
                            ->join('store', 'sale_factor.store_id', '=', 'store.store_id')
                            ->where('store.agency_id', '=', \Auth::user()->agency_id)
                            ->whereBetween('sale_product.sale_date', [$start_date, $end_date])
                            ->sum(DB::raw("(sale_price - buy_price)"));
                    }


//                $income = ($sum_of_total_payment) - ($sum_of_sale_factor + $sum_of_expense + $sum_of_salary);
                    $data = [
                        'sum_of_buy_factor' => $sum_of_buy_factor,
                        'sum_of_salary_payment' => $sum_of_salary_payment,
                        'total_salary' => $sum_of_salary_payment,
                        'total_car_income' => $sum_of_car_income,
                        'total_expense' => $sum_of_expense,
                        'total_sale_factor' => $sum_of_sale_factor,
                        "sum_of_benif" => ($sum_of_benif + $sum_of_car_income) - ($sum_of_salary_payment + $sum_of_expense)
                    ];


                    $finalData = $data;

                }

                $owner = Owner::all()->where("status", "<", "1");


                DB::commit();
                return response()->json(array([
                        "owner" => $owner,
                        "finaldata" => $finalData

                    ])
                );

            }
            catch (\Exception $e){

                DB::rollBack();
                return response()->json(array([
                        "owner" => [],
                        "finaldata" => []

                    ])
                );
            }



        }

    }


}
