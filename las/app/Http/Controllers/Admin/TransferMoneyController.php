<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\MoneyStore;
use App\Models\Store;
use App\Models\StoreMoney;
use App\Models\TransferMoney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;

class TransferMoneyController extends Controller
{
    public function index($id = null)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $moneyTransfers = TransferMoney::where('status', '!=', 1)->paginate($id);
            } else {
                $moneyTransfers = TransferMoney::where('status', '!=', 1)->paginate(5);
            }
        } else {
            if (isset($id) && $id > 0) {
                $moneyTransfers = TransferMoney::join('money_store', 'sender_id', '=', 'store_id')
                    ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                    ->where('transfer_money.status', '!=', 1)
                    ->paginate($id);
            } else {
                $moneyTransfers = TransferMoney::join('money_store', 'sender_id', '=', 'store_id')
                    ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                    ->where('transfer_money.status', '!=', 1)
                    ->paginate(5);
            }
        }
        return view('transfer_money.index', compact('moneyTransfers'))->with(['panel_title' => 'لیست انتقالات پول', 'route' => route('money_transfer.list')]);
    }


    public function create(Request $request)
    {
        if ($request->isMethod('get')) {
            $moneyStores = StoreMoney::all();
            return view('transfer_money.form', compact('moneyStores'))->with('panel_title', 'انتفال پول ');
        } else {
            $validator = Validator::make(Input::all(), [
                'sender_account_id' => 'required',
                'receiver_account_id' => 'required',
                'payment_amount' => 'required',
                'rate' => 'required',
                'pr_date' => 'required',
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                DB::beginTransaction();
                try {

                    if ($request->input('sender_account_id') == $request->input('receiver_account_id')) {
                        return array(
                            'fail' => true,
                            'errors' => ['حساب ها باهم مشابه می باشد لطفا حساب های متفاوت انتخاب کنید']
                        );

                    } else {

                        $transferMoney = new TransferMoney();
                        $transferMoney->sender_id = Input::get('sender_account_id');
                        $transferMoney->receiver_id = Input::get('receiver_account_id');
                        $transferMoney->payment_amount = Input::get('payment_amount');
                        $transferMoney->rate = Input::get('rate');
                        $transferMoney->date = Input::get('pr_date');
                        $transferMoney->description = Input::get('transfer_description');

                        $sender_store =
                            DB::table('money_store')
                                ->where('money_store.store_id', '=', $transferMoney->sender_id)
                                ->first();


                        if ($sender_store->money_amount > 0) {
                            $payment = (($transferMoney->payment_amount) * ($transferMoney->rate));

                            StoreMoney::where('store_id', $transferMoney->sender_id)->decrement('money_amount', $transferMoney->payment_amount);
                            StoreMoney::where('store_id', $transferMoney->receiver_id)->increment('money_amount', $payment);

                            $transferMoney->save();

                        } else {
                            return array(
                                'fail' => true,
                                'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                            );
                        }


                        DB::commit();
                        return array(
                            'content' => 'content',
                            'url' => route('money_transfer.list')
                        );
//
                    }
//
                } catch (\Exception $e) {

                    DB::rollBack();
                    return array(
                        'fail' => true,
                        'errors' => "خطا"
                    );

                }

            }
        }
    }

    public function update(Request $request, $id)
    {

        $transferMoney = TransferMoney::find($id);
        if ($request->isMethod('get')) {
            $moneyStores = StoreMoney::all();
            $currencies = Currency::all();
            return view('transfer_money.form', compact('transferMoney', 'currencies', 'moneyStores'))->with(['panel_title' => 'ویرایش انتقال حساب']);
        } else {
            $validator = Validator::make(Input::all(), [
                'sender_account_id' => 'required',
                'receiver_account_id' => 'required',
                'payment_amount' => 'required',
                'rate' => 'required',
                'pr_date' => 'required',
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {


                DB::beginTransaction();


                try {


                    $transferMoney = TransferMoney::find($id);
                    $payment = (($transferMoney->payment_amount) * ($transferMoney->rate));

                    $re = StoreMoney::where('store_id', $transferMoney->sender_id);
                    $re->increment('money_amount',$payment );


                    $re = StoreMoney::where('store_id', $transferMoney->receiver_id);
                    $re->decrement('money_amount',$transferMoney->payment_amount );


                    if ($request->input('sender_account_id') == $request->input('receiver_account_id')) {
                        return array(
                            'fail' => true,
                            'errors' => ['حساب ها باهم مشابه می باشد لطفا حساب های متفاوت انتخاب کنید']
                        );

                    } else {


                        $transferMoney->sender_id = Input::get('sender_account_id');
                        $transferMoney->receiver_id = Input::get('receiver_account_id');
                        $transferMoney->payment_amount = Input::get('payment_amount');
                        $transferMoney->rate = Input::get('rate');
                        $transferMoney->date = Input::get('pr_date');
                        $transferMoney->description = Input::get('transfer_description');


                        $sender_store = DB::table('money_store')
                                ->where('money_store.store_id', '=', $transferMoney->sender_id)
                                ->first();

                        if ($sender_store->money_amount > 0) {

                            $payment = (($transferMoney->payment_amount) * ($transferMoney->rate));
                            StoreMoney::where('store_id', $transferMoney->sender_id)->decrement('money_amount', $transferMoney->payment_amount);
                            StoreMoney::where('store_id', $transferMoney->receiver_id)->increment('money_amount', $payment);
                            $transferMoney->save();

                        } else {
                            return array(
                                'fail' => true,
                                'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                            );
                        }


                        DB::commit();
                        return array(
                            'content' => 'content',
                            'url' => route('money_transfer.list')
                        );
                    }

                } catch (\Exception $e) {

                    DB::rollBack();
                    return array(
                        'fail' => true,
                        'errors' => "خطا"
                    );



                }
            }
        }
    }

    public function delete($id)
    {
        $transferMoney = TransferMoney::find($id);
        $transferMoney->delete();
        return redirect()->route('money_transfer.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
    }

    public function report()
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $money_stores = StoreMoney::all();
        } else {
            $money_stores = StoreMoney::where('agency_id', '=', \Auth::user()->agency_id)
                ->where('status', '=', 0)
                ->get();
        }
        return view('transfer_money.report', compact('money_stores'))->with('panel_title', 'گزارشات  به زمانهای مختلف');

    }

    public function report_data(Request $request, $id = null)
    {
        if ($request->ajax()) {

            $final_sum = 0.0;
            $final_data = array();

            $output = '';
            $store = $request->get('store');
            $type_of_store = $request->get('type_of_store');
            $type = $request->get('type');
            $y = $request->get('year');

            $data = null;
            $sum = null;

            if ($type === 'day') {

                if ($type_of_store == 'money_sender') {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->where('date', date('Y-m-d'))
                            ->paginate($id);
                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->where('date', date('Y-m-d'))
                            ->paginate(5);
                    }

                    $sum = DB::table('transfer_money')
                        ->where('sender_id', $store)
                        ->where('date', date('Y-m-d'))
                        ->sum('payment_amount');

                } else {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->where('date', date('Y-m-d'))
                            ->paginate($id);
                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->where('date', date('Y-m-d'))
                            ->paginate(5);
                    }

                    $sum = DB::table('transfer_money')
                        ->where('receiver_id', $store)
                        ->where('date', date('Y-m-d'))
                        ->sum('payment_amount');
                }

                $finalData = [
                    'data' => $data,
                    'store' => $sum,
                ];

            } elseif ($type === 'week') {


                $startOfWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y/m/d');
                $endOfWeek = \Carbon\Carbon::now()->endOfWeek()->format('Y/m/d');


                if ($type_of_store = 'money_sender') {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$startOfWeek, $endOfWeek])
                            ->paginate($id);

                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$startOfWeek, $endOfWeek])
                            ->paginate(5);
                    }

                    $sum = DB::table('transfer_money')
                        ->where('sender_id', $store)
                        ->whereBetween('date', [$startOfWeek, $endOfWeek])
                        ->sum('payment_amount');
                } else {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$startOfWeek, $endOfWeek])
                            ->paginate($id);
                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$startOfWeek, $endOfWeek])
                            ->paginate(5);
                    }

                    $sum = DB::table('transfer_money')
                        ->where('receiver_id', $store)
                        ->whereBetween('date', [$startOfWeek, $endOfWeek])
                        ->sum('payment_amount');
                }

                $finalData = [
                    'data' => $data,
                    'store' => $sum,
                ];

            } elseif ($type === 'month') {

                $jyear = date("Y");
                $jmonth = $request->get('month_r');;
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


                if ($type_of_store == 'money_sender') {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->paginate($id);

                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->paginate(5);
                    }


                    $sum = \DB::table('transfer_money')
                        ->where('sender_id', $store)
                        ->whereBetween('date', [$start_date, $end_date])
                        ->sum('payment_amount');
                } else {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->paginate($id);
                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->paginate(5);
                    }

                    $sum = \DB::table('transfer_money')
                        ->where('receiver_id', $store)
                        ->whereBetween('date', [$start_date, $end_date])
                        ->sum('payment_amount');
                }

                $finalData = [

                    'data' => $data,
                    'store' => $sum,
                ];

            } elseif ($type === 'year') {

                $getyear = $request->get('year_r');
                $yaer_date = explode('-', $getyear);
                $final_year = $yaer_date[0];
                $startfrom = $final_year . '-01-01';
                $end = $final_year . '-12-30';


                if ($type_of_store == 'money_sender') {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$startfrom, $end])
                            ->paginate($id);
                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'receiver_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$startfrom, $end])
                            ->paginate(5);
                    }


                    $sum = \DB::table('transfer_money')
                        ->where('sender_id', $store)
                        ->whereBetween('date', [$startfrom, $end])
                        ->sum('payment_amount');
                } else {
                    if (isset($id) && $id > 0) {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$startfrom, $end])
                            ->paginate($id);
                    } else {
                        $data = DB::table('transfer_money')
                            ->join('money_store', 'store_id', 'sender_id')
                            ->select('name', 'payment_amount', 'rate', 'date')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$startfrom, $end])
                            ->paginate(5);
                    }

                    $sum = \DB::table('transfer_money')
                        ->where('receiver_id', $store)
                        ->whereBetween('date', [$startfrom, $end])
                        ->sum('payment_amount');
                }

                $finalData = [

                    'data' => $data,
                    'store' => $sum,
                ];

            } elseif ($type === 'bt_date') {

                if ($request->get('year_r') != '') {
                    $start_date = $request->get('year_r');
                    $end_date = $request->get('end-year');


                    if ($type_of_store == 'money_sender') {
                        if (isset($id) && $id > 0) {
                            $data = DB::table('transfer_money')
                                ->join('money_store', 'store_id', 'receiver_id')
                                ->select('name', 'payment_amount', 'rate', 'date')
                                ->where('sender_id', $store)
                                ->whereBetween('date', [$start_date, $end_date])
                                ->paginate($id);
                        } else {
                            $data = DB::table('transfer_money')
                                ->join('money_store', 'store_id', 'receiver_id')
                                ->select('name', 'payment_amount', 'rate', 'date')
                                ->where('sender_id', $store)
                                ->whereBetween('date', [$start_date, $end_date])
                                ->paginate(5);
                        }

                        $sum = DB::table('transfer_money')
                            ->where('sender_id', $store)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->sum('payment_amount');
                    } else {
                        if (isset($id) && $id > 0) {
                            $data = DB::table('transfer_money')
                                ->join('money_store', 'store_id', 'sender_id')
                                ->select('name', 'payment_amount', 'rate', 'date')
                                ->where('receiver_id', $store)
                                ->whereBetween('date', [$start_date, $end_date])
                                ->paginate($id);
                        } else {
                            $data = DB::table('transfer_money')
                                ->join('money_store', 'store_id', 'sender_id')
                                ->select('name', 'payment_amount', 'rate', 'date')
                                ->where('receiver_id', $store)
                                ->whereBetween('date', [$start_date, $end_date])
                                ->paginate(5);
                        }

                        $sum = DB::table('transfer_money')
                            ->where('receiver_id', $store)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->sum('payment_amount');
                    }
                    $finalData = [
                        'data' => $data,
                        'store' => $sum,
                    ];
                }

            }

            return \Response::JSON(array(
                'data' => $data,
                'sum' => $sum,
                "pagination" => (string)$data->links(),
            ));

        }

    }



    public function search($id)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $data = \DB::table('transfer_money')
                ->join('money_store', 'store_id', '=', 'sender_id')
                ->select("transfer_money.transfer_id", "transfer_money.sender_id", "transfer_money.receiver_id", "transfer_money.payment_amount", "transfer_money.rate", "transfer_money.date", "transfer_money.description", "money_store.name")
                ->where('transfer_id', 'LIKE', "%$id%")
                ->orWhere('sender_id', 'LIKE', "%$id%")
                ->orWhere('receiver_id', 'LIKE', "%$id%")
                ->orWhere('name', 'LIKE', "%$id%")
                ->orWhere('payment_amount', 'LIKE', "%$id%")
                ->orWhere('rate', 'LIKE', "%$id%")
                ->orWhere('date', 'LIKE', "%$id%")
                ->where('description', 'LIKE', "%$id%")
                ->where('transfer_money.status', '=', 0)
                ->get();
        } else {
            $data = \DB::table('transfer_money')
                ->join('money_store', 'sender_id', '=', 'store_id')
                ->select("transfer_money.transfer_id", "transfer_money.sender_id", "transfer_money.receiver_id", "transfer_money.payment_amount", "transfer_money.rate", "transfer_money.date", "transfer_money.description", "money_store.name", "money_store.store_id")
                ->where('money_store.agency_id', '=', \Auth::user()->agency_id)
                ->where(function ($q) use ($id) {
                    $q->where('transfer_id', 'LIKE', "%$id%")
                        ->orWhere('sender_id', 'LIKE', "%$id%")
                        ->orWhere('receiver_id', 'LIKE', "%$id%")
                        ->orWhere('name', 'LIKE', "%$id%")
                        ->orWhere('payment_amount', 'LIKE', "%$id%")
                        ->orWhere('rate', 'LIKE', "%$id%")
                        ->orWhere('date', 'LIKE', "%$id%")
                        ->where('description', 'LIKE', "%$id%");
                })
                ->where('transfer_money.status', '=', 0)
                ->get();
        }

        if (count($data) > 0) {
            return response(array(
                'data' => $data,
            ));
        }

    }

}

