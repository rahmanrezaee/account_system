<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Currency;
use App\Models\CustomerPayment;
use App\models\CustomerPaymentReport;
use App\Models\MoneyStore;
use App\Models\ReserveHall;
use App\Models\Store;
use App\Models\TotalPayment;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Sale_Factor;
use App\Models\Customer;
use Validator;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Carbon;


class CustomerController extends Controller
{
    public function index($id = null)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $customer = Customer::where('status', '<', 1)->paginate($id);

            } else {
                $customer = Customer::where('status', '<', 1)->paginate(5);
            }
        } else {
            if (isset($id) && $id > 0) {
                $customer = Customer::where('status', '<', 1)->where('agency_id', '=', Auth::user()->agency_id)->paginate($id);
            } else {
                $customer = Customer::where('status', '<', 1)->where('agency_id', '=', Auth::user()->agency_id)->paginate(5);

            }
        }

        return view('customer.index', compact('customer'))->with(['panel_title' => 'لیست مشتریان', "route" => route('customer.list')]);

    }

    public function create(Request $request)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $agencies = Agency::all();
        } else {
            $agencies = Agency::where('agency_id', '=', Auth::user()->agency_id)->get();
        }
        if ($request->isMethod('get'))

            return view('customer.form', compact('agencies'))->with(['panel_title' => 'ایجاد مشتری جدید']);
        else {

            $validator = Validator::make($request->all(), [
                    'name' => 'required|min:3',
                    'phone' => 'required|min:10|max:13',
                    'address' => 'required'
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $customer = new Customer();
            $customer->name = Input::get('name');
            $customer->phone = Input::get('phone');
            $customer->address = Input::get('address');
            $customer->customer_code = Input::get('customer_code');
            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                $customer->agency_id = Input::get('agency_id');
            } else {
                $customer->agency_id = Auth::user()->agency_id;
            }
            $customer->save();

            return array(
                'content' => 'content',
                'url' => route('customer.create')
            );


        }

    }

    public function update(Request $request, $id)
    {

        $customer = Customer::find($id);
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $agencies = Agency::all();
        } else {
            $agencies = Agency::where('agency_id', '=', Auth::user()->agency_id)->get();
        }
        if ($request->isMethod('get'))

            return view('customer.form', compact('customer', 'agencies'))->with(['panel_title' => 'ویرایش مشتری ']);
        else {
            $validator = Validator::make(Input::all(), [
                    'name' => 'required|min:3',
                    'phone' => 'required|min:10|max:50',
                    'address' => 'required',
                    'customer_code' => 'required',

                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $data = [
                'name' => Input::get('name'),
                'phone' => Input::get('phone'),
                'address' => Input::get('address'),
                'customer_code' => Input::get('customer_code')
            ];

            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                $data['agency_id'] = Input::get('agency_id');
            } else {
                $data['agency_id'] = Auth::user()->agency_id;
            }
            Customer::find($id)->update($data);

            return array(

                'content' => 'content',
                'url' => route('customer.list')
            );

        }

    }

    public function delete($id)
    {


        if ($id && ctype_digit($id)) {


            DB::table("customer")->where('customer_id', $id)->update(['status' => 1]);
            return redirect()->route("customer.list")->with('success', 'عملیه حذف با موفقیتت انجام شد');
        }

    }

    public function paymentUpdate(Request $request, $id)
    {
        $totalPayment = TotalPayment::find($id);
        if ($request->isMethod('get'))

            return view('customer.customer_payment_update', compact('totalPayment'))->with(['panel_title' => 'ویرایش پرداخت مشتری ']);
        else {
            $validator = Validator::make(Input::all(), [
                    'total_payment' => 'required',
                    'current_payment' => 'required',
                    'payment_date' => 'required'
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $data = [
                'total_payment' => Input::get('total_payment'),
                'current_payment' => Input::get('current_payment'),
                'date' => Input::get('payment_date')
            ];
            TotalPayment::find($id)->update($data);
            return array(

                'content' => 'content',
                'url' => route('customer_show_payment')
            );
        }
    }

    public function cusGetDecorRemain($id, $res_hall_id)
    {
        $decor = 'دیکوریشن';
        $decorRemainder = 0.0;
        $decorInfo = DB::table('res_decoration')->select('res_decoration.customer_id', 'res_decor_id', 'total_payment')
            ->where('res_decoration.customer_id', '=', $id)
            ->orderBy('res_decoration.customer_id', 'desc')->first();
        if (isset($decorInfo)) {
            $decorPayment = DB::table('reserve_payment')
                ->where('reserve_payment.reserve_id', '=', $res_hall_id)
                ->where('reserve_type', '=', $decor)
                ->sum('payment_amount');
            $decorRemainder = ($decorInfo->total_payment - $decorPayment);
        }
        return $decorRemainder;
    }

    public function cusGetMusicRemain($id, $res_hall_id)
    {
        $music = 'موسیقی';
        $musicRemainder = 0.0;
        $musicInfo = DB::table('reserve_music')->select('reserve_music.customer_id', 'res_music_id', 'total_payment')
            ->where('reserve_music.customer_id', '=', $id)
            ->orderBy('reserve_music.customer_id', 'desc')->first();

        if (isset($musicInfo)) {
            $musicPayment = DB::table('reserve_payment')
                ->where('reserve_payment.reserve_id', '=', $res_hall_id)
                ->where('reserve_type', '=', $music)
                ->sum('payment_amount');
            $musicRemainder = ($musicInfo->total_payment - $musicPayment);
        }
        return $musicRemainder;
    }

    public function cusGetFilmRemain($id, $res_hall_id)
    {
        $film = 'فلمبردار';
        $filmRemainder = 0.0;
        $filmInfo = DB::table('reserve_film')->select('reserve_film.customer_id', 'res_film_id', 'total_payment')
            ->where('reserve_film.customer_id', '=', $id)
            ->orderBy('reserve_film.customer_id', 'desc')->first();
        if (isset($filmInfo)) {
            $filmPayment = DB::table('reserve_payment')
                ->where('reserve_payment.reserve_id', '=', $res_hall_id)
                ->where('reserve_type', '=', $film)
                ->sum('payment_amount');

            $filmRemainder = ($filmInfo->total_payment - $filmPayment);
        }

        return $filmRemainder;
    }

    public function customerGetPayment(Request $request, $id)
    {
        $hall = 'سالون';


        $hallInfo = \DB::table('reserve_hall')->select('reserve_hall.customer_id', 'customer.name', 'res_hall_id', 'total_payment')
            ->join('customer', 'customer.customer_id', '=', 'reserve_hall.customer_id')
            ->where('reserve_hall.customer_id', '=', $id)
            ->orderBy('reserve_hall.customer_id', 'desc')->first();

        $hallPayment = \DB::table('reserve_payment')
            ->where('reserve_payment.reserve_id', '=', $hallInfo->res_hall_id)
            ->where('reserve_type', '=', $hall)
            ->sum('payment_amount');

        $hallRemainder = $hallInfo->total_payment - $hallPayment;

        if ($hallInfo) {

            $decorRemainder = $this->cusGetDecorRemain($id, $hallInfo->res_hall_id);

            $musicRemainder = $this->cusGetMusicRemain($id, $hallInfo->res_hall_id);

            $filmRemainder = $this->cusGetFilmRemain($id, $hallInfo->res_hall_id);
        } else {
            $decorRemainder = $musicRemainder = $filmRemainder = '';
        }

        if ($request->isMethod('get'))

            return view('customer.customer_payment_remainder', compact('hallInfo', 'hallRemainder', 'decorRemainder', 'musicRemainder', 'filmRemainder'))->with(['panel_title' => 'پرداخت اقساط بدهی مشتری ']);


    }

    public function customerPayment(Request $request)
    {

        $validator = Validator::make(Input::all(), [
                'payment_id' => 'required',
                'customer_id' => 'required',
                'payment_date' => 'required',
                'current_payment' => 'required'
            ]
        );
        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        }
        $currentPay = Input::get('current_payment');
        $resPaymentId = Input::get('payment_id');


        $cusPay = new CustomerPayment();
        $cusPay->reserve_id = Input::get('payment_id');
        $cusPay->customer_id = Input::get('customer_id');
        $cusPay->reserve_type = Input::get('payment_type');
        $cusPay->payment_amount = Input::get('current_payment');
        $cusPay->date = Input::get('payment_date');
        $cusPay->description = Input::get('description');

        if ($cusPay->save()) {
            $totalPayment = \DB::table('total_payment')->select('total_payment_id', 'total_payment', 'current_payment')
                ->where('reserve_id', '=', $resPaymentId)->first();
            TotalPayment::find($totalPayment->total_payment_id)->
            update(array('current_payment' => ($totalPayment->current_payment + $currentPay)));
        }
        return array(
            'content' => 'content',
            'url' => route('customer_show_payment')
        );


    }

    public function showDetailsPayment($id)
    {
        $payment = CustomerPayment::all()
            ->where('reserve_id', '=', $id);
        $total = $payment->sum('payment_amount');
        return view('customer.show_details_payment', compact('payment', 'total'))->with(['panel_title' => 'لیست پرداختی مشتری']);
    }

    public function detailsPaymentUpdate(Request $request, $id)
    {
        $detailsPayment = CustomerPayment::find($id);
        if ($request->isMethod('get'))
            return view('customer.customer_detailse_payment_update', compact('detailsPayment'))->with(['panel_title' => 'ویرایش پرداخت مشتری ']);
        else {
            $validator = Validator::make(Input::all(), [
                    'payment_amount' => 'required',
                    'payment_date' => 'required'
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $data = [
                'payment_amount' => Input::get('payment_amount'),
                'date' => Input::get('payment_date')
            ];
            CustomerPayment::find($id)->update($data);
            return array(

                'content' => 'content',
                'url' => route('customer_show_payment')
            );
        }
    }

    public function customerShowPayment()
    {
        $customer = Customer::select('customer.customer_id', 'total_payment_id', 'reserve_id', 'name', 'phone', 'current_payment', 'total_payment')
            ->join('total_payment', 'total_payment.customer_id', '=', 'customer.customer_id')
            ->orderBy('reserve_id', 'desc')
            ->where('customer.status', '!=', '1')
            ->get();

        return view('customer.customerShowPayment', compact('customer'))->with('panel_title', 'لیست بدهی مشتریان');
    }

    public function remindPayment()
    {

        $currencies = Currency::all()->where("status", "0");
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $customers = Customer::all()->where("status", "<", "1");
        } else {
            $customers = Customer::all()->where("status", "<", "1")->where('agency_id', Auth::user()->agency_id);
        }


        return view("customer.remindPay", compact("customers", 'currencies'))->with('panel_title', 'بدیهی های مشتریان');


    }

    public function paymentCustomer(Request $request)
    {

        $validator = Validator::make($request->all(), [
                'payment_amount' => 'required',
                'date_payment' => 'required',
                'currency_rate' => 'required',
            ]
        );
        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        }

        // factor recieve money edit
        $re = Sale_Factor::find($request->input("sale_factor_id"));

        $remin = $re->total_price - ($re->recieption_price + $re->discount);

        if ($remin < $request->input("payment_amount")) {

            return array(
                'fail' => true,
                'errors' => array(["مقدار وارده معتبر نیست!!"])
            );

        }

        $re->increment("recieption_price", $request->input("payment_amount"));


        $money_store_id = $re->money_store_id;

        $money_current = MoneyStore::find($money_store_id);
        $money_current->decrement("money_amount", $request->input("currency_rate") * $request->input("payment_amount"));

        if ($re) {

            CustomerPaymentReport::create([
                "sale_factor_id" => $request->input("sale_factor_id"),
                "payment_amount" => $request->input("payment_amount"),
                "date" => $request->input("date_payment"),
                "type" => 'CustomerPayment',
                "table_related" => "sale_factor",
                "currency_id" => $re->currency_id,
                "currency_rate" => $request->input("currency_rate"),
                "currency_main_id" => $re->currency_main_id,
            ]);

            return array(
                'content' => 'content',
                'url' => route("customer.remindPayment")
            );
        }


    }

    public function listRemindCustomer($id)
    {

        $factorPayment = DB::table('customer_payment')
            ->join("currency", 'currency.currency_id', 'customer_payment.currency_id')
            ->select("customer_payment.*", "currency.currency_name")
            ->where('sale_factor_id', $id)
            ->where('table_related', 'sale_factor')->get();

        $total = DB::table('customer_payment')
            ->where('sale_factor_id', $id)
            ->where('table_related', 'sale_factor')
            ->sum("payment_amount");


        $remind = DB::table('customer_payment')
            ->where('sale_factor_id', $id)
            ->where('table_related', 'sale_factor')
            ->sum("payment_amount");


        return response()->json(array('data' => $factorPayment, "total" => $total, "remind", $remind));

    }

    public function get_customer_report()
    {

        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $customers = Customer::where("status", "<", 1)->get();
        } else {
            $customers = Customer::where("status", "<", 1)->where('agency_id', '=', Auth::user()->agency_id)->get();
        }
        return view("customer.report", compact("customers"))->with('panel_title', 'گزارش مشتریان');

    }

    public function customerReport(Request $request, $id = null)
    {

        if ($request->ajax()) {
            $finalData = array();
            $total_recieption = '';
            $total = '';
            $income = 0.0;
            $output = '';
            $type = $request->get('type');

            if ($request->input("reason") == 'all') {

                // Daily report
                if ($type == 'day') {


                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->where('sale_date', date("Y-m-d"))
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->where('sale_date', date("Y-m-d"))
                            ->sum('recieption_price');
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->where('sale_date', date("Y-m-d"))
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', date("Y-m-d"))
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', date("Y-m-d"))
                            ->sum('recieption_price');
                    }

                    $total_recieption = $recieption_price;
                    $total = $total_price;

                    $finalData = $data;
                }
                // weekly report
                if ($type == 'week') {


                    $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                    $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->sum('recieption_price');
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->sum('recieption_price');
                    }

                    $total_recieption = $recieption_price;
                    $total = $total_price;
                    $finalData = $data;
                }
                // monthly report
                if ($type === 'month') {
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


                        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = \DB::table('sale_factor')
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }

                            $total_price = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('total_price');

                            $recieption_price = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('recieption_price');
                        } else {
                            if (isset($id) && $id > 0) {
                                $data = \DB::table('sale_factor')
                                    ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                    ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate($id);
                            } else {
                                $data = \DB::table('sale_factor')
                                    ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                    ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                    ->whereBetween('sale_date', [$start_date, $end_date])
                                    ->paginate(5);
                            }

                            $total_price = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('total_price');

                            $recieption_price = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->sum('recieption_price');
                        }

                        $total_recieption = $recieption_price;
                        $total = $total_price;

                        $finalData = $data;

                    }

                }
                // yearly report
                if ($type === 'year') {

                    $get_year = $request->get('year_r');


                    $yaer_date = explode('-', $get_year);
                    $final_year = $yaer_date[0];
                    $start_date = $final_year . '-01-01';
                    $end_date = $final_year . '-12-30';


                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('recieption_price');
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('recieption_price');
                    }

                    $total_recieption = $recieption_price;
                    $total = $total_price;

                    $finalData = $data;

                }
                // between tow date report
                if ($type === 'bt_date') {

                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('recieption_price');
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                                ->where('customer.agency_id', '=', Auth::user()->agency_id)
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->join('customer', 'sale_factor.customer_id', '=', 'customer.customer_id')
                            ->where('customer.agency_id', '=', Auth::user()->agency_id)
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->sum('recieption_price');
                    }

                    $total_recieption = $recieption_price;
                    $total = $total_price;
                    $finalData = $data;
                }
            } else {
                // Daily report
                if ($type == 'day') {


                    if (isset($id) && $id > 0) {
                        $data = \DB::table('sale_factor')
                            ->where('sale_date', date("Y-m-d"))
                            ->where("customer_id", $request->input("reason"))
                            ->paginate($id);
                    } else {
                        $data = \DB::table('sale_factor')
                            ->where('sale_date', date("Y-m-d"))
                            ->where("customer_id", $request->input("reason"))
                            ->paginate(5);
                    }

                    $total_price = \DB::table('sale_factor')
                        ->where('sale_date', date("Y-m-d"))
                        ->where("customer_id", $request->input("reason"))
                        ->sum('total_price');

                    $recieption_price = \DB::table('sale_factor')
                        ->where('sale_date', date("Y-m-d"))
                        ->where("customer_id", $request->input("reason"))
                        ->sum('recieption_price');

                    $total_recieption = $recieption_price;
                    $total = $total_price;
                    $finalData = $data;
                }
                // weekly report
                if ($type == 'week') {

                    $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                    $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');

                    if (isset($id) && $id > 0) {
                        $data = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->where("customer_id", $request->input("reason"))
                            ->paginate($id);
                    } else {
                        $data = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                            ->where("customer_id", $request->input("reason"))
                            ->paginate(5);
                    }


                    $total_price = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                        ->where("customer_id", $request->input("reason"))
                        ->sum('total_price');

                    $recieption_price = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                        ->where("customer_id", $request->input("reason"))
                        ->sum('recieption_price');

                    $total_recieption = $recieption_price;
                    $total = $total_price;
                    $finalData = $data;
                }
                // monthly report
                if ($type === 'month') {
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
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->where("customer_id", $request->input("reason"))
                                ->paginate($id);
                        } else {
                            $data = \DB::table('sale_factor')
                                ->whereBetween('sale_date', [$start_date, $end_date])
                                ->where("customer_id", $request->input("reason"))
                                ->paginate(5);
                        }

                        $total_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->where("customer_id", $request->input("reason"))
                            ->sum('total_price');

                        $recieption_price = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->where("customer_id", $request->input("reason"))
                            ->sum('recieption_price');

                        $total_recieption = $recieption_price;
                        $total = $total_price;
                        $finalData = $data;
                    }

                }
                // yearly report
                if ($type === 'year') {

                    $get_year = $request->get('year_r');
                    $yaer_date = explode('-', $get_year);
                    $final_year = $yaer_date[0];
                    $start_date = $final_year . '-01-01';
                    $end_date = $final_year . '-12-30';


                    if (isset($id) && $id > 0) {
                        $data = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->where("customer_id", $request->input("reason"))
                            ->paginate($id);
                    } else {
                        $data = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->where("customer_id", $request->input("reason"))
                            ->paginate(5);
                    }

                    $total_price = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$start_date, $end_date])
                        ->where("customer_id", $request->input("reason"))
                        ->sum('total_price');

                    $recieption_price = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$start_date, $end_date])
                        ->where("customer_id", $request->input("reason"))
                        ->sum('recieption_price');

                    $total_recieption = $recieption_price;
                    $total = $total_price;
                    $finalData = $data;

                }
                // between tow date report
                if ($type === 'bt_date') {

                    $start_date = $request->get('start_date');
                    $end_date = $request->get('end_date');

                    if (isset($id) && $id > 0) {
                        $data = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->where("customer_id", $request->input("reason"))
                            ->paginate($id);
                    } else {
                        $data = \DB::table('sale_factor')
                            ->whereBetween('sale_date', [$start_date, $end_date])
                            ->where("customer_id", $request->input("reason"))
                            ->paginate(5);
                    }

                    $total_price = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$start_date, $end_date])
                        ->where("customer_id", $request->input("reason"))
                        ->sum('total_price');

                    $recieption_price = \DB::table('sale_factor')
                        ->whereBetween('sale_date', [$start_date, $end_date])
                        ->where("customer_id", $request->input("reason"))
                        ->sum('recieption_price');

                    $total_recieption = $recieption_price;
                    $total = $total_price;
                    $finalData = $data;
                }


            }
        }
    }

    public function search($id)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $data = \DB::table('customer')
                ->select('customer.customer_id', 'customer.name', 'customer.customer_type', 'customer.phone', 'customer.address')
                ->where('customer.customer_id', 'LIKE', "%$id%")
                ->orWhere('customer.name', 'LIKE', "%$id%")
                ->orWhere('customer.address', 'LIKE', "%$id%")
                ->where('customer.status', '!=', 1)
                ->get();

        } else {
            $data = \DB::table('customer')
                ->join('agency', 'agency.agency_id', '=', 'customer.agency_id')
                ->select('customer.customer_id', 'customer.name', 'customer.customer_type', 'customer.phone', 'customer.address', 'agency_name')
                ->where('customer.agency_id', '=', \Auth::user()->agency_id)
                ->where(function ($q) use ($id) {
                    $q->where('customer.customer_id', 'LIKE', "%$id%")
                        ->orWhere('customer.name', 'LIKE', "%$id%")
                        ->orWhere('customer.address', 'LIKE', "%$id%");
                })
                ->where('customer.status', '!=', 1)
                ->get();
        }

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }
    }

}