<?php

namespace App\Http\Controllers\Admin;

use App\Models\CatchMoney;
use App\Models\Employee;
use App\Models\MoneyStore;
use App\Models\StoreMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;

class CatchMoneyController extends Controller
{
    //
    public function index()
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
        {
            $employees = Employee::all();
        } else {
            $employees = Employee::where('agency_id','=',\Auth::user()->agency_id)
                ->where('status','=',0)
                ->get();
        }
        $catchMoney = CatchMoney::where('status','=', 0)->get();
        return view('catch_money.index', compact('employees','catchMoney'))->with(['panel_title' => 'لیست برداشت و پرداخت پول', 'route' => route('catch_money.list')]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))
        {
            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
            {
                $moneyStores = StoreMoney::all();
                $employees = Employee::all();
            } else {
                $moneyStores = StoreMoney::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status','=',0)
                    ->get();
                $employees = Employee::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status','=',0)
                    ->get();
            }
            return view('catch_money.form', compact('moneyStores','employees'))->with('panel_title','برداشت و پرداخت پول');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'catch_amount' => 'required',
                'account_id' => 'required',
                'catch_date' => 'required',
                'employee_id' => 'required',
//                'catch_money_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                DB::beginTransaction();

                try{

                    $catchMoney = new CatchMoney();
                    $catchMoney->amount = $request->get('catch_amount');
                    $catchMoney->account_type = $request->get('account_id');
                    $catchMoney->date = $request->get('catch_date');
                    $catchMoney->employee_id = $request->get('employee_id');
                    $catchMoney->status_payment = $request->get('payment_status');
                    $catchMoney->description = $request->get('catch_money_description');

                    if ($request->get('payment_status') == 'draw')
                    {
                        $money = StoreMoney::where('money_amount','>=' ,$request->input('catch_amount'))->where('store_id', $request->get('account_id'))->get();
                        if (count($money) > 0){
                            StoreMoney::where('store_id', $catchMoney->account_type)->decrement('money_amount', $request->get('catch_amount'));
                            $catchMoney->save();
                        } else{
                            return array(
                                'fail' => true,
                                'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                            );
                        }
                    } elseif ($request->get('payment_status') == 'pay') {
                        StoreMoney::where('store_id', $catchMoney->account_type)->increment('money_amount', $request->get('catch_amount'));
                        $catchMoney->save();
                    }
                    DB::commit();
                    return array(
                        'content' => 'content',
                        'url' => route('catch_money.list')
                    );

                }catch (\Exception $e){

                    DB::rollBack();

                    return array(
                        'fail' => true,
                        'errors' => "انجام نشد"
                    );
                }


            }
        }
    }

    public function update(Request $request, $id)
    {
        $catchMoney = CatchMoney::find($id);
        if ($request->isMethod('get'))
        {
            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
            {
                $moneyStores = StoreMoney::all();
                $employees = Employee::all();
            } else {
                $moneyStores = StoreMoney::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status','=',0)
                    ->get();
                $employees = Employee::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status','=',0)
                    ->get();
            }
            return view('catch_money.form', compact('moneyStores','catchMoney','employees'))->with('panel_title','ویرایش برداشت و پرداخت پول');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'catch_amount' => 'required',
                'account_id' => 'required',
                'catch_date' => 'required',
                'employee_id' => 'required',
//                'catch_money_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $catchMoney =CatchMoney::find($id);
                if ($catchMoney->status_payment == 'draw')
                {
                    $m_s = StoreMoney::where('store_id', $catchMoney->account_type)->increment('money_amount', $catchMoney->amount);
                    if (count($m_s) > 0)
                    {
                        $money = StoreMoney::where('money_amount','>=' ,$request->input('catch_amount'))->where('store_id', $request->get('account_id'))->get();
                        if (count($money) > 0){

                            StoreMoney::where('store_id', Input::get('account_id'))->decrement('money_amount', Input::get('catch_amount'));

                            $catchMoney->amount = $request->get('catch_amount');
                            $catchMoney->account_type = $request->get('account_id');
                            $catchMoney->date = $request->get('catch_date');
                            $catchMoney->employee_id = $request->get('employee_id');
                            $catchMoney->description = $request->get('catch_money_description');
                            $catchMoney->status_payment = $request->get('payment_status');
                            $catchMoney->update();
                        } else{
                            return array(
                                'fail' => true,
                                'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                            );
                        }
                    }

                } elseif ($catchMoney->status_payment == 'pay') {

                    StoreMoney::where('store_id', $catchMoney->account_type)->decrement('money_amount', $catchMoney->amount);
                    StoreMoney::where('store_id', Input::get('account_id'))->increment('money_amount', Input::get('catch_amount'));

                    $catchMoney->amount = $request->get('catch_amount');
                    $catchMoney->account_type = $request->get('account_id');
                    $catchMoney->date = $request->get('catch_date');
                    $catchMoney->employee_id = $request->get('employee_id');
                    $catchMoney->description = $request->get('catch_money_description');
                    $catchMoney->update();

                }

                return array(
                    'content' => 'content',
                    'url' => route('catch_money.list')
                );

            }
        }
    }

    public function delete($id)
    {
        $catchMoney = CatchMoney::find($id);
        $catchMoney->status = 1;
        $catchMoney->update();
        return redirect()->route('catch_money.list')->with('success','با موفقیت حذف شد ');
    }

    public function report()
    {
        $money_stores = StoreMoney::all();
        return view('catch_money.report', compact('money_stores'))->with('panel_title', 'گزارشات  به زمانهای مختلف');

    }

    public function report_data(Request $request)
    {
        if ($request->ajax()) {

            $final_sum = 0.0;
            $final_data = array();

            $output = '';
            $store = $request->get('store');
            $type = $request->get('type');
            $y = $request->get('year');


            if ($type === 'day') {

                $jyear = Jalalian::fromCarbon(Carbon::now())->getYear();
                $jmonth = Jalalian::fromCarbon(Carbon::now())->getMonth();
                $jday = Jalalian::fromCarbon(Carbon::now())->getDay();

                $date = '';
                if (intval($jmonth )< 10 && intval($jday )> 9) {
                    $date= $jyear . '-0' . $jmonth . '-' . $jday;
                } elseif (intval($jday) < 10 && intval($jmonth )> 9) {
                    $date = $jyear . '-' . $jmonth . '-0' . $jday;

                } elseif (intval($jmonth) < 10 && intval($jday) < 10) {
                    $date = $jyear . '-0' . $jmonth . '-0' . $jday;
                }

                $data = DB::table('catch_money')
                    ->join('money_store','store_id','account_type')
                    ->select('amount','date', 'description')
                    ->where('account_type', $store)
                    ->where('date', $date)
                    ->get();

                $sum = DB::table('catch_money')
                    ->where('date', $date)
                    ->sum('amount');

                $finalData = [

                    'data' => $data,
                    'store' => $sum,
                ];

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
                }



                $data = DB::table('catch_money')
                    ->join('money_store','store_id','account_type')
                    ->select('amount','date', 'description')
                    ->where('account_type', $store)
                    ->whereBetween('date', [$jdate, $date])
                    ->get();


                $sum = DB::table('catch_money')
                    ->whereBetween('date', [$jdate, $date])
                    ->sum('amount');

                $finalData = [

                    'data' => $data,
                    'store' => $sum,
                ];

            }
            elseif ($type === 'month') {
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

                $data = DB::table('catch_money')
                    ->join('money_store','store_id','account_type')
                    ->select('amount','date', 'description')
                    ->where('account_type', $store)
                    ->whereBetween('date', [$start_month_date, $jdate])
                    ->get();


                $sum = \DB::table('catch_money')
                    ->whereBetween('date', [$start_month_date, $jdate])
                    ->sum('amount');

                $finalData = [

                    'data' => $data,
                    'store' => $sum,
                ];

            } elseif ($type === 'year') {

                $getyear = $request->get('year_r');
                $yaer_date = explode('/', $getyear);
                $final_year = $yaer_date[0];
                $startfrom = $final_year . '-01-01';
                $end = $final_year . '-12-30';


                $data = DB::table('catch_money')
                    ->join('money_store','store_id','account_type')
                    ->select('amount','date', 'description')
                    ->where('account_type', $store)
                    ->whereBetween('date', [$startfrom, $end])
                    ->get();

                $sum = \DB::table('catch_money')
                    ->whereBetween('date', [$startfrom, $end])
                    ->sum('amount');

                $finalData = [

                    'data' => $data,
                    'store' => $sum,
                ];

            } elseif ($type === 'bt_date') {
                if ($request->get('start_date') != '') {
                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');


                    $data = DB::table('catch_money')
                        ->join('money_store','store_id','account_type')
                        ->select('amount','date', 'description')
                        ->where('account_type', $store)
                        ->whereBetween('date', [$start_date, $end_date])
                        ->get();

                    $sum = DB::table('catch_money')
                        ->where('account_type', $store)
                        ->whereBetween('date', [$start_date, $end_date])
                        ->sum('amount');

                    $finalData = [
                        'data' => $data,
                        'store' => $sum,
                    ];
                }
            }

            if (count($finalData) > 0) {
                return response()->json($finalData);
            }
        }
    }

    public function search(Request $request, $employee_id, $status_payment,$id = null)
    {


        if (isset($id) && $id > 0)
        {
            $data = DB::table('catch_money')
                ->join('money_store', 'account_type', '=', 'store_id')
                ->join('employee', 'catch_money.employee_id', '=', 'employee.employee_id')
                ->select('catch_money.id', 'catch_money.amount', 'catch_money.date', 'catch_money.description', 'catch_money.status_payment', 'employee.first_name', 'employee.last_name', 'money_store.name')
                ->where('catch_money.employee_id', '=', $employee_id)
                ->where('catch_money.amount','>',0)
                ->where('catch_money.status','=',0)
                ->where('status_payment','=',$status_payment)
                ->paginate($id);

        } else {



            $data = DB::table('catch_money')
                ->join('money_store', 'account_type', '=', 'store_id')
                ->join('employee', 'catch_money.employee_id', '=', 'employee.employee_id')
                ->select('catch_money.id', 'catch_money.amount', 'catch_money.date', 'catch_money.description', 'catch_money.status_payment', 'employee.first_name', 'employee.last_name', 'money_store.name')
                ->where('catch_money.employee_id', '=', $employee_id)
                ->where('catch_money.amount','>',0)
                ->where('catch_money.status','=',0)
                ->where('status_payment','=',$status_payment)
                ->paginate(5);

//            dd($data);


        }


        $count_of_payment = DB::table('catch_money')
            ->where('employee_id','=',$employee_id)
            ->where('status_payment','=','pay')
            ->sum('amount');
        $count_of_catch = DB::table('catch_money')
            ->where('employee_id','=',$employee_id)
            ->where('status_payment','=','draw')
            ->sum('amount');

            return \Response::JSON(array('count_of_payment' => $count_of_payment,
                'count_of_catch' => $count_of_catch,
                'data' => $data,
                'pagination' => (string) $data->links(),
                'route' => route('catch_money.list'),
            ));

    }

}
