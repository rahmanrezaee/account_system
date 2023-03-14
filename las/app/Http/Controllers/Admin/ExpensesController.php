<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\Expense;
use App\Models\Reason_Pay;
use App\Models\StoreMoney;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;

class ExpensesController extends Controller
{


    public function index($id = null)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $expenses = Expense::select("expense_id", "title", "amount", "pay_date", "currency_name", 'currency_rate', "description")
                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                    ->join('currency', 'currency.currency_id', '=', 'expense.currency_id')
                    ->where("expense.status", "!=", "1")
                    ->paginate($id);
            } else {
                $expenses = Expense::select("expense_id", "title", "amount", "pay_date", "currency_name", 'currency_rate', "description")
                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                    ->join('currency', 'currency.currency_id', '=', 'expense.currency_id')
                    ->where("expense.status", "!=", "1")->paginate(5);
            }
        } else {
            if (isset($id) && $id > 0) {
                $expenses = Expense::select("expense_id", "title", "amount", "pay_date", "currency_name", 'currency_rate', "description")
                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                    ->join('currency', 'currency.currency_id', '=', 'expense.currency_id')
                    ->where("expense.status", "!=", "1")
                    ->paginate($id);
            } else {
                $expenses = Expense::select("expense_id", "title", "amount", "pay_date", "currency_name", 'currency_rate', "description")
                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                    ->join('currency', 'currency.currency_id', '=', 'expense.currency_id')
                    ->where("expense.status", "!=", "1")
                    ->paginate(5);
            }
        }

        return view('expense.index', compact('expenses'))->with(['panel_title' => 'لیست مصارف', 'route' => route('expense.list')]);
    }

    public function create(Request $request)
    {


        $reasons = Reason_Pay::all()->where('status', '!=', 1);

        $currencies = Currency::all()->where("status", "0");


        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $moneyStores = StoreMoney::all()->where('status', '!=', 1);
        } else {
            $moneyStores = StoreMoney::where('agency_id', '=', Auth::user()->agency_id)
                ->where('status', '!=', 1)
                ->get();
        }

        if ($request->isMethod('get'))
            return view('expense.form', compact('reasons', 'moneyStores', 'currencies'))->with('panel_title', 'ثبت مصارف ');
        else {

            $validator = Validator::make(Input::all(), [
                'title' => 'required',
                'amount' => 'required',
                'currency_id' => 'required',
                'pay_date' => 'required',
                'currency_rate' => 'required'
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $ex = new Expense();
                $ex->expense_reason_id = Input::get('title');
                $ex->description = Input::get('description');
                $ex->amount = Input::get('amount');
                $ex->currency_id = Input::get('currency_id');
                $ex->currency_rate = Input::get('currency_rate');
                $ex->currency_main_id = get_options("mainCurrency")->value('option_value');
                $ex->pay_date = Input::get('pay_date');
                $ex->account_id = Input::get('account');
                $ex->user_id = Session::get('user_id');

                $money = StoreMoney::find($request->get('account'));

                $resultMount = Input::get('currency_rate') * Input::get('amount');


                if ($money->money_amount > $resultMount) {

                    $money->decrement('money_amount', $resultMount);
                    $money->save();

                    $ex->save();

                } else {
                    return array(
                        'fail' => true,
                        'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                    );
                }
                return array(
                    'content' => 'content',
                    'url' => route('expense.create')
                );


            }
        }
    }


    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);
        if ($request->isMethod('get')) {

            $reasons = Reason_Pay::all();

            $currencies = Currency::all()->where("status", "0");

            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                $moneyStores = StoreMoney::all()->where('status', '!=', 1);
            } else {
                $moneyStores = StoreMoney::where('agency_id', '=', Auth::user()->agency_id)
                    ->where('status', '!=', 1)
                    ->get();
            }
            return view('expense.form', compact('expense', 'reasons', 'moneyStores', 'currencies'))->with('panel_title', 'ویرایش مصرف ');

        } else {
            $validator = Validator::make(Input::all(), [
                'title' => 'required',
                'amount' => 'required',
                'currency_id' => 'required',
                'pay_date' => 'required',
                'currency_rate' => 'required'
            ]);


            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {


                // subtract the Money store

                $resultMount = $expense->currency_rate * $expense->amount;


                $money = StoreMoney::find($expense->account_id);


                $money->increment('money_amount', $resultMount);


                // update expense

                $expense->expense_reason_id = Input::get('title');
                $expense->description = Input::get('description');
                $expense->currency_id = Input::get('currency_id');
                $expense->currency_rate = Input::get('currency_rate');
                $expense->currency_main_id = get_options("mainCurrency")->value('option_value');
                $expense->pay_date = Input::get('pay_date');
                $expense->account_id = Input::get('account');
                $expense->user_id = Session::get('user_id');


                //update the Money Store

                $money = StoreMoney::find(Input::get('account'));

                $resultMount = Input::get('currency_rate') * Input::get('amount');
                if ($money->money_amount > $resultMount) {

                    $money->decrement('money_amount', $resultMount);
                    $expense->amount = Input::get('amount');

                    $expense->save();

                } else {
                    return array(
                        'fail' => true,
                        'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                    );
                }
                return array(
                    'content' => 'content',
                    'url' => route('expense.create')
                );


            }
        }

    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            Expense::find($id)->where('expense_id', $id)->update(['status' => 1]);
            return redirect()->route('expense.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
        }

    }

    public function report()
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $reason_pays = Reason_Pay::all()->where('status', '!=', 1);
        } else {
            $reason_pays = Reason_Pay::join('expense', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                ->where('expense_reason.status', '!=', 1)
                ->get();
        }
        return view('expense.report', compact('reason_pays'))->with('panel_title', 'گذارشات مصارف به زمانهای مختلف');

    }

    public function report_data(Request $request, $id = null)
    {
        if ($request->ajax()) {

            $final_sum = 0.0;
            $final_data = array();

            $output = '';
            $reason = $request->get('reason');
            $type = $request->get('type');
            $y = $request->get('year');


            // reason_pay
            if ($reason === 'all') {

                if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                    if ($type === 'day') {

                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                        $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                        $date = '';
                        if (intval($jmonth) < 10 && intval($jday) > 9) {
                            $date = $jyear . '-0' . $jmonth . '-' . $jday;
                        } elseif (intval($jday) < 10 && intval($jmonth) > 9) {
                            $date = $jyear . '-' . $jmonth . '-0' . $jday;

                        } elseif (intval($jmonth) < 10 && intval($jday) < 10) {
                            $date = $jyear . '-0' . $jmonth . '-0' . $jday;
                        } else {

                            $jdate = $jyear . '-' . $jmonth . '-' . $jday;
                        }

                        if (isset($id) && $id > 0) {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('pay_date', $date)
                                ->paginate($id);
                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')

                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('pay_date', $date)
                                ->paginate(5);
                        }

                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')

                            ->where('pay_date', $date)
                            ->sum('amount');

                        $final_sum = $sum;
                        $final_data = $data;

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

                        if (isset($id) && $id > 0) {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$jdate, $date])
                                ->paginate($id);
                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$jdate, $date])
                                ->paginate(5);
                        }


                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                            ->whereBetween('pay_date', [$jdate, $date])
                            ->sum('amount');

                        $final_data = $data;
                        $final_sum = $sum;
                    } elseif ($type === 'month') {
                        $jmonth = $request->get('month_r');
                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                        $start_month_date = '';
                        if ($jmonth < 10) {
                            $start_month_date = $jyear . '-0' . $jmonth . '-01';
                        } else {
                            $start_month_date = $jyear . '-' . $jmonth . '-01';
                        }


                        $jdate = '';
                        if ($jmonth < 10 && $jday > 9) {
                            $jdate = $jyear . '-0' . $jmonth . '-' . $jday;
                        } elseif ($jday < 10 && $jmonth > 9) {
                            $jdate = $jyear . '-' . $jmonth . '-0' . $jday;

                        } elseif ($jmonth < 10 && $jday < 10) {
                            $jdate = $jyear . '-0' . $jmonth . '-0' . $jday;
                        }
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$start_month_date, $jdate])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')

                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$start_month_date, $jdate])
                                ->paginate(5);
                        }


                        $sum = \DB::table('expense')
                            ->whereBetween('pay_date', [$start_month_date, $jdate])
                            ->sum('amount');
                        $final_data = $data;
                        $final_sum = $sum;

                    } elseif ($type === 'year') {


                        $getyear = $request->get('year_r');
                        $yaer_date = explode('/', $getyear);
                        $final_year = $yaer_date[0];
                        $startfrom = $final_year . '-01-01';
                        $end = $final_year . '-12-31';

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$startfrom, $end])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')

                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$startfrom, $end])
                                ->paginate(5);
                        }

                        $sum = \DB::table('expense')
                            ->whereBetween('pay_date', [$startfrom, $end])
                            ->sum('amount');
                        $final_data = $data;
                        $final_sum = $sum;

                    } elseif ($type === 'bt_date') {
                        if ($request->get('start_date') != '') {
                            $start_date = $request->get('start_date');
                            $end_date = $request->get('end_date');

                            if (isset($id) && $id > 0) {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }

                            $sum = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->whereBetween('pay_date', [$start_date, $end_date])
                                ->sum('amount');
                            $final_data = $data;
                            $final_sum = $sum;

                        }

                    }


                    $total_row = count($final_data);


                    return \Response::JSON(array(
                        'table_data' => $final_data,
                        'total_data' => $total_row,
                        'sum' => $final_sum,

                    ));

                } else {
                    if ($type === 'day') {

                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                        $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                        $date = '';
                        if (intval($jmonth) < 10 && intval($jday) > 9) {
                            $date = $jyear . '-0' . $jmonth . '-' . $jday;
                        } elseif (intval($jday) < 10 && intval($jmonth) > 9) {
                            $date = $jyear . '-' . $jmonth . '-0' . $jday;

                        } elseif (intval($jmonth) < 10 && intval($jday) < 10) {
                            $date = $jyear . '-0' . $jmonth . '-0' . $jday;
                        } else {

                            $jdate = $jyear . '-' . $jmonth . '-' . $jday;
                        }

                        if (isset($id) && $id > 0) {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('pay_date', $date)
                                ->paginate($id);
                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('pay_date', $date)
                                ->paginate(5);
                        }

                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                            ->where('pay_date', $date)
                            ->sum('amount');

                        $final_sum = $sum;
                        $final_data = $data;

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

                        if (isset($id) && $id > 0) {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$jdate, $date])
                                ->paginate($id);
                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$jdate, $date])
                                ->paginate(5);
                        }


                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                            ->whereBetween('pay_date', [$jdate, $date])
                            ->sum('amount');

                        $final_data = $data;
                        $final_sum = $sum;
                    } elseif ($type === 'month') {
                        $jmonth = $request->get('month_r');
                        $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                        $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                        $start_month_date = '';
                        if ($jmonth < 10) {
                            $start_month_date = $jyear . '-0' . $jmonth . '-01';
                        } else {
                            $start_month_date = $jyear . '-' . $jmonth . '-01';
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

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$start_month_date, $jdate])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$start_month_date, $jdate])
                                ->paginate(5);
                        }


                        $sum = \DB::table('expense')
                            ->whereBetween('pay_date', [$start_month_date, $jdate])
                            ->sum('amount');
                        $final_data = $data;
                        $final_sum = $sum;

                    } elseif ($type === 'year') {

                        $getyear = $request->get('year_r');
                        $yaer_date = explode('/', $getyear);
                        $final_year = $yaer_date[0];
                        $startfrom = $final_year . '-01-01';
                        $end = $final_year . '-12-31';

                        if (isset($id) && $id > 0) {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$startfrom, $end])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$startfrom, $end])
                                ->paginate(5);
                        }

                        $sum = \DB::table('expense')
                            ->whereBetween('pay_date', [$startfrom, $end])
                            ->sum('amount');
                        $final_data = $data;
                        $final_sum = $sum;

                    } elseif ($type === 'bt_date') {
                        if ($request->get('start_date') != '') {
                            $start_date = $request->get('start_date');
                            $end_date = $request->get('end_date');

                            if (isset($id) && $id > 0) {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate($id);

                            } else {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }

                            $sum = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->whereBetween('pay_date', [$start_date, $end_date])
                                ->sum('amount');
                            $final_data = $data;
                            $final_sum = $sum;

                        }

                    }


                    $total_row = count($final_data);

                    return \Response::JSON(array(
                        'table_data' => $final_data,
                        'total_data' => $total_row,
                        'sum' => $final_sum,
                        "pagination" => (string)$final_data->links(),
                    ));
                }
            }//after all expense data
            else {

                if (ctype_digit($reason)) {
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

                        if (isset($id) && $id > 0) {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('expense_reason.expense_reason_id', $reason)
                                ->where('pay_date', $jdate)
                                ->paginate($id);
                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('expense_reason.expense_reason_id', $reason)
                                ->where('pay_date', $jdate)
                                ->paginate(5);
                        }

                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                            ->where('expense_reason.expense_reason_id', $reason)
                            ->where('pay_date', $jdate)
                            ->sum('amount');


                    }
                    elseif ($type === 'week') {
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

                        if (isset($id) && $id > 0) {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$jdate, $date])
                                ->where('expense_reason.xpense_reason_id', $reason)
                                ->paginate($id);
                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->whereBetween('pay_date', [$jdate, $date])
                                ->where('expense_reason.expense_reason_id', $reason)
                                ->paginate(5);
                        }

                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                            ->whereBetween('pay_date', [$jdate, $date])
                            ->where('expense_reason.expense_reason_id', $reason)
                            ->sum('amount');


                    }
                    elseif ($type === 'month') {
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

                            if (isset($id) && $id > 0) {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->where('expense_reason.expense_reason_id', $reason)
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->where('expense_reason.expense_reason_id', $reason)
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }


                            $sum = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->where('expense_reason.expense_reason_id', $reason)
                                ->whereBetween('pay_date', [$start_date, $end_date])
                                ->sum('amount');


                        }
                    }
                    elseif ($type === 'year') {

                        $getyear = $request->get('year_r');
                        $yaer_date = explode('/', $getyear);
                        $final_year = $yaer_date[0];
                        $startfrom = $final_year . '-01-01';
                        $end = $final_year . '-12-31';


                        if (isset($id) && $id > 0) {

                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('expense_reason.expense_reason_id', $reason)
                                ->whereBetween('pay_date', [$startfrom, $end])
                                ->paginate($id);

                        } else {
                            $data = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                ->where('expense_reason.expense_reason_id', $reason)
                                ->whereBetween('pay_date', [$startfrom, $end])
                                ->paginate(5);
                        }


                        $sum = DB::table('expense')
                            ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')

                            ->where('expense_reason.expense_reason_id', $reason)
                            ->whereBetween('pay_date', [$startfrom, $end])
                            ->sum('amount');


                    }
                    elseif ($type === 'bt_date') {
                        if ($request->get('start_date') != '') {
                            $start_date = $request->get('start_date');
                            $end_date = $request->get('end_date');

                            if (isset($id) && $id > 0) {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->where('expense_reason.expense_reason_id', $reason)
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = DB::table('expense')
                                    ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                                    ->join('currency',"expense.currency_id","currency.currency_id")
                                    ->select("expense_id", "title", "amount", "pay_date", "currency.currency_name", "description")
                                    ->where('expense_reason.expense_reason_id', $reason)
                                    ->whereBetween('pay_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }

                            $sum = DB::table('expense')
                                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')

                                ->where('expense_reason.expense_reason_id', $reason)
                                ->whereBetween('pay_date', [$start_date, $end_date])
                                ->sum('amount');


                        }

                    }


                }

                $total_row = $data->count();

                return \Response::JSON(array(
                    'table_data' => $data,
                    'total_data' => $total_row,
                    'sum' => $sum,

                ));


            }

        }

    }

    public function search($id)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $data = DB::table('expense')
                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                ->select("expense_id", "title", "amount", "pay_date", "currency_id", "description" )
                ->where('expense_id', 'LIKE', "%$id%")
                ->orWhere('title', 'LIKE', "%$id%")
                ->orWhere('amount', 'LIKE', "%$id%")
                ->orWhere('pay_date', 'LIKE', "%$id%")
                ->orWhere('currency_id', 'LIKE', "%$id%")
                ->orWhere('description', 'LIKE', "%$id%")
                ->where("expense.status", "!=", "1")
                ->get();
        } else {
            $data = DB::table('expense')
                ->join('expense_reason', 'expense_reason.expense_reason_id', '=', 'expense.expense_reason_id')
                ->join('currency',"expense.currency_id","currency.currency_id")
                                ->select("expense_id", "expense_reason.title", "amount", "pay_date", "currency.currency_name", "description")
                ->where(function ($q) use ($id) {
                    $q->where('expense_id', 'LIKE', "%$id%")
                        ->orWhere('title', 'LIKE', "%$id%")
                        ->orWhere('amount', 'LIKE', "%$id%")
                        ->orWhere('pay_date', 'LIKE', "%$id%")
                        ->orWhere('currency_id', 'LIKE', "%$id%")
                        ->orWhere('description', 'LIKE', "%$id%");

                })
                ->where("expense.status", "!=", "1")
                ->get();
        }

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }

}
