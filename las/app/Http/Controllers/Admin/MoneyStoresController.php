<?php

namespace App\Http\Controllers\Admin;


use App\Models\Agency;
use App\Models\BuyFactor;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\MoneyStore;
use App\Models\Currency;
use App\Models\SalaryPayment;
use App\Models\Sale_Factor;
use App\Models\StoreMoney;
use App\Models\Owner;
use App\Models\TransferMoney;
use Carbon\Carbon;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;

class MoneyStoresController extends Controller
{
    public function index($id = null)
    {

        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $moneyStores = StoreMoney::where('status', '<', 1)->paginate($id);

            } else {
                $moneyStores = StoreMoney::where('status', '<', 1)->paginate(5);
            }
        } else {
            if (isset($id) && $id > 0) {
                $moneyStores = StoreMoney::where('agency_id', '=', \Auth::user()->agency_id)
                    ->where('status', '<', 1)
                    ->paginate($id);
            } else {
                $moneyStores = StoreMoney::where('agency_id', '=', \Auth::user()->agency_id)
                    ->where('status', '<', 1)
                    ->paginate(5);
            }
        }

        return view('money_store.index', compact('moneyStores'))->with(['panel_title' => 'لیست موجودی ذخیره شده در حساب ها', 'route' => route('money_store.list')]);
    }

    public function create(Request $request)
    {

        if ($request->isMethod('get')) {
            if (\Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                $agencies = Agency::all();
            } else {
                $agencies = Agency::where('agency_id', '=', Auth::user()->agency_id)->get();
            }
            $currencies = Currency::all();
            return view('money_store.form', compact('currencies', 'agencies'))->with('panel_title', 'اضافه کردن حساب');
        } else {
            $validator = Validator::make(Input::all(), [
                'account_type' => 'required',
                'amount' => 'required',
                'currency_id' => 'required',
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $moneyStore = new StoreMoney();
                $moneyStore->name = Input::get('account_type');
                $moneyStore->money_amount = Input::get('amount');
                $moneyStore->currency_id = Input::get('currency_id');

                if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                    $moneyStore->agency_id = $request->input("agency_id");
                } else {
                    $moneyStore->agency_id = \Auth::user()->agency_id;
                }
                $moneyStore->save();
                return array(
                    'content' => 'content',
                    'url' => route('money_store.list')
                );
            }
        }
    }

    public function update(Request $request, $id)
    {

        $moneyStores = MoneyStore::find($id);

        $agencies = Agency::all();
        if ($request->isMethod('get')) {
            $employees = Owner::select('full_name', 'owner_id')->where('status', '!=', 1)->get();
            return view('money_store.form', compact('employees', 'moneyStores', 'agencies'))->with('panel_title', 'ویرایش پرداخت یا برداشت پول');
        } else {
            $validator = Validator::make(Input::all(), [
                'payment_type' => 'required',
                'owner_id' => 'required',
                'current_payment' => 'required',
                'payment_date' => 'required'
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $money = MoneyStore::find($id);
                $money->payment_type = Input::get('payment_type');
                $money->payment_amount = Input::get('current_payment');
                $money->owner_id = Input::get('owner_id');
                $money->date = Input::get('payment_date');
                if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
                    $money->agency_id = $request->input("agency_id");
                } else {
                    $money->agency_id = \Auth::user()->agency_id;
                }

                $money->save();
                return array(
                    'content' => 'content',
                    'url' => route('money_store.list')
                );
            }
        }
    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            MoneyStore::find($id)->where('money_store_id', $id)->update(['status' => 1]);
            return redirect()->route('money_store.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
        }
    }

    public function paymentChangeStatus($id)
    {
        if ($id && ctype_digit($id)) {
            MoneyStore::find($id)->where('money_store_id', $id)->update(['payment_status' => 'تصفیه شده']);
            return redirect('money_store')->with('success', 'عملیه تصفیه حساب با موفقیتت انجام شد');
        }
    }

    public function report()
    {
        $stores = \DB::table('money_store')
            ->join('owner', 'owner.owner_id', '=', 'money_store.owner_id')
            ->select('money_store.money_store_id', 'owner.full_name', 'money_store.owner_id', 'payment_type')
            ->where('money_store.status', '!=', 1)->get();
        return view('money_store.report_form', compact('stores'));

    }

    public function searchMoneyStoreReport(Request $request)
    {
        $employee_id = $request->get('owner_id');
        $payment_type = $request->get('payment_type');
        \DB::statement(\DB::raw("set @raw:=0"));

//        $user = \DB::table('money_store')->select(\DB::raw("@row:=@row+1 as rowNumber"))
//        ->get();

        if ($payment_type == 'all') {
            $store = \DB::table('money_store')
                ->join('owner', 'owner.owner_id', '=', 'money_store.owner_id')
                ->select('money_store.money_store_id', 'owner.full_name', 'money_store.owner_id', 'payment_type', 'payment_amount', 'date', 'payment_status'
                    , \DB::raw('sum(payment_amount) as total_money'))
                ->where('money_store.status', '!=', 1)
                ->where('money_store.owner_id', '=', $employee_id)
                ->groupBy('employee.full_name', 'money_store.employee_id', 'payment_type', 'payment_amount', 'date', 'payment_status', 'money_store_id')
                ->get();
        } else {
            $store = \DB::table('money_store')
                ->join('owner', 'owner.owner_id', '=', 'money_store.owner_id')
                ->select('money_store.money_store_id', 'owner.full_name', 'money_store.owner_id', 'payment_type', 'payment_amount', 'date', 'payment_status'
                    , \DB::raw('sum(payment_amount) as total_money'))
                ->where('money_store.status', '!=', 1)
                ->where('payment_type', '=', "$payment_type")
                ->where('money_store.owner_id', '=', $employee_id)
                ->groupBy('owner.full_name', 'money_store.owner_id', 'payment_type', 'payment_amount', 'date', 'payment_status', 'money_store_id')
                ->get();
        }

        $output = '';
        $qty = 0;
        foreach ($store as $key => $str) {
            $qty += $str->total_money;
            $output .= '<tr>';
            $output .= '<td>' . ++$key . '</td>';
            $output .= '<td>' . $str->full_name . '</td>';
            $output .= '<td>' . $str->date . '</td>';
            $output .= '<td>' . $str->payment_amount . '</td>';
            $output .= '<td>' . $str->payment_type . '</td>';
            $output .= '<td>' . $str->payment_status . '</td>';
            $output .= '</tr>';
        }

        return array(
            'output' => $store,
            'total' => $qty,
        );

    }

    public function search($id)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0) {
            $data = \DB::table('money_store')
                ->join('currency', 'currency.currency_id', '=', 'money_store.currency_id')
                ->select("money_store.store_id", "money_store.name", "money_store.money_amount", "money_store.currency_id", "currency.currency_name")
                ->where('money_store.store_id', 'LIKE', "%$id%")
                ->orWhere('money_store.name', 'LIKE', "%$id%")
                ->orWhere('money_store.money_amount', 'LIKE', "%$id%")
                ->orWhere('money_store.currency_id', 'LIKE', "%$id%")
                ->orWhere('currency.currency_name', 'LIKE', "%$id%")
                ->where("money_store.status", "!=", "1")
                ->get();
        } else {
            $data = \DB::table('money_store')
                ->join('currency', 'currency.currency_id', '=', 'money_store.currency_id')
                ->select("money_store.store_id", "money_store.name", "money_store.money_amount", "money_store.currency_id", "currency.currency_name")
                ->where('agency_id', '=', \Auth::user()->agency_id)
                ->where(function ($q) use ($id) {
                    $q->where('money_store.store_id', 'LIKE', "%$id%")
                        ->orWhere('money_store.name', 'LIKE', "%$id%")
                        ->orWhere('money_store.money_amount', 'LIKE', "%$id%")
                        ->orWhere('money_store.currency_id', 'LIKE', "%$id%")
                        ->orWhere('currency.currency_name', 'LIKE', "%$id%");
                })
                ->where("money_store.status", "!=", "1")
                ->get();
        }


        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }

    public function resumecha(Request $request,$id = null)
    {


        if ($request->isMethod('get')) {

            return view('money_store.resumecha');

        } else {


            $resumecha = array();

            $footer = array();


            $jtoday = $request->input('end_date');
            $jtodayDate = $request->input('start_date');




            if ($request->input("state") == 'expense') {

                $result = Expense::whereBetween('pay_date', [$jtodayDate, $jtoday])->get();

                $receiver_money = null;
                $drawer_money = null;
                $total_money = null;

                foreach ($result as $re) {

                    array_push($resumecha,
                        [
                            'account_name' => getAccountName($re->account_id),
                            'payment_date' => $re->pay_date,
                            'description' => "پرداخت شده مصارف",
                            'mount' => $re->amount,
                            'currency' => getCurrencyName($re->currency_main_id),
                            'rate' => $re->currency_rate,
                            'mountexchanged' => ($re->currency_rate * $re->amount),
                            'rateexchanged' => $re->currency_rate,
                            'currencyexchanged' => getCurrencyName($re->currency_id),
                            'accountexchanged' => "متفرقه",
                        ]
                    );

                    if (isset($drawer_money[getCurrency($re->currency_id)])) {

                        $drawer_money[getCurrency($re->currency_id)] += $re->amount;
                        $receiver_money[getCurrency($re->currency_id)] += 0;


                    } else {

                        $drawer_money[getCurrency($re->currency_id)] = $re->amount;
                        $receiver_money[getCurrency($re->currency_id)] = 0;


                    }


                    if (isset($total_money[getCurrency($re->currency_id)])) {


                        $total_money[getCurrency($re->currency_id)] += $re->amount;

                    } else {

                        $total_money[getCurrency($re->currency_id)] = $re->amount;

                    }



                    $footer['receiver_money'] = $receiver_money;
                    $footer['drawer_money'] = $drawer_money;
                    $footer['total_money'] = $total_money;


                }

            }
            else if ($request->input('state') == 'customerPayment') {


                $result = CustomerPayment::whereBetween('date', [$jtodayDate, $jtoday])
                    ->orderBy('date',"DESC")
                    ->get();


                $receiver_money = null;
                $drawer_money = null;
                $total_money = null;


                if (count($result)){


                    foreach ($result as $re) {


                        if ($re->table_related == 'sale_factor') {

                            $currenct = Sale_Factor::find($re->sale_factor_id);


                            array_push($resumecha,
                                [
                                    'account_name' => getAccountName($currenct->money_store_id),
                                    'payment_date' => $re->date,
                                    'description' => "پرداخت شده مشتری",
                                    'mount' => $re->payment_amount,
                                    'currency' => getCurrencyName($re->currency_id),
                                    'rate' => $re->currency_rate,
                                    'mountexchanged' => ($re->currency_rate * $re->amount),
                                    'rateexchanged' => $re->currency_rate,
                                    'currencyexchanged' => getCurrencyName($re->currency_main_id),
                                    'accountexchanged' => "متفرقه",
                                ]
                            );


                            if (isset($receiver_money[getCurrency($re->currency_id)])) {

                                $receiver_money[getCurrency($re->currency_id)] += $re->payment_amount;

                            } else {

                                $receiver_money[getCurrency($re->currency_id)] = $re->payment_amount;

                            }

                            if (isset($total_money[getCurrency($re->currency_id)])) {


                                $total_money[getCurrency($re->currency_id)] += $re->payment_amount;

                            } else {

                                $total_money[getCurrency($re->currency_id)] = $re->payment_amount;

                            }



                            $footer['receiver_money'] = $receiver_money;


                        }
                        else if ($re->table_related == 'buy_factor') {



                            $currenct = BuyFactor::find($re->sale_factor_id);



                            array_push($resumecha,
                                [
                                    'account_name' => "متفرقه",
                                    'payment_date' => $re->date,
                                    'description' => "پرداخت شده مشتری",
                                    'mount' => $re->payment_amount,
                                    'currency' => getCurrencyName($re->currency_id),
                                    'rate' => $re->currency_rate,
                                    'mountexchanged' => ($re->currency_rate * $re->amount),
                                    'rateexchanged' => $re->currency_rate,
                                    'currencyexchanged' => getCurrencyName($re->currency_main_id),
                                    'accountexchanged' => isset($currenct->money_store_id) ? getAccountName($currenct->money_store_id): "null",
                                ]
                            );

                            if (isset($drawer_money[getCurrency($re->currency_id)])) {

                                $drawer_money[getCurrency($re->currency_id)] += $re->payment_amount;

                            } else {


                                $drawer_money[getCurrency($re->currency_id)] = $re->payment_amount;

                            }


                            if (isset($total_money[getCurrency($re->currency_id)])) {


                                $total_money[getCurrency($re->currency_id)] += $re->payment_amount;

                            } else {

                                $total_money[getCurrency($re->currency_id)] = $re->payment_amount;

                            }


                            $footer['drawer_money'] = $drawer_money;

                        }

                        $footer['total_money'] = $total_money;
                    }
                }



            }
            else if ($request->input('state') == 'employee') {


                $result = SalaryPayment::whereBetween('payment_date', [$jtodayDate, $jtoday])
                    ->get();


                $receiver_money = null;
                $drawer_money = null;
                $total_money = null;


                foreach ($result as $re) {


                    array_push($resumecha,
                        [
                            'account_name' => getAccountName($re->account_id),
                            'payment_date' => $re->payment_date,
                            'description' => "پرداخت شده معاش کارمند",
                            'mount' => $re->payment_amount,
                            'currency' => getCurrencyName($re->currency_main_id),
                            'rate' => $re->currency_rate,
                            'mountexchanged' => ($re->currency_rate * $re->payment_amount),
                            'rateexchanged' => $re->currency_rate,
                            'currencyexchanged' => getCurrencyName($re->currency_id),
                            'accountexchanged' => "متفرقه",
                        ]
                    );


                    if (isset($drawer_money[getCurrency($re->currency_id)])) {

                        $drawer_money[getCurrency($re->currency_id)] += $re->payment_amount;
                        $receiver_money[getCurrency($re->currency_id)] += 0;



                    } else {

                        $drawer_money[getCurrency($re->currency_id)] = $re->payment_amount;
                        $receiver_money[getCurrency($re->currency_id)] = 0;

                    }



                    if (isset($total_money[getCurrency($re->currency_id)])) {


                        $total_money[getCurrency($re->currency_id)] += $re->payment_amount;

                    } else {

                        $total_money[getCurrency($re->currency_id)] = $re->payment_amount;

                    }


                    $footer['receiver_money'] = $receiver_money;
                    $footer['drawer_money'] = $drawer_money;
                    $footer['total_money'] = $total_money;


                }


            }
            else if ($request->input('state') == 'transferMoney') {


                $result = TransferMoney::whereBetween('date', [$jtodayDate, $jtoday])
                    ->get();

                foreach ($result as $re) {


                    array_push($resumecha,
                        [
                            'account_name' => getAccountName($re->sender_id),
                            'payment_date' => $re->date,
                            'description' => "انتقال پول",
                            'mount' => $re->payment_amount,
                            'currency' => getCurrency(MoneyStore::find($re->sender_id)->currency_id),
                            'rate' => $re->rate,
                            'mountexchanged' => ($re->rate * $re->payment_amount),
                            'rateexchanged' => $re->rate,
                            'currencyexchanged' => getCurrency(MoneyStore::find($re->receiver_id)->currency_id),
                            'accountexchanged' => getAccountName($re->receiver_id),
                        ]
                    );


                    $footer['receiver_money'] = ['ندارد' ];
                    $footer['drawer_money'] = ['ندارد'];

                    $footer['total_money'] = ['ندارد'];

                }

            }


            return \Response()->json([$resumecha,$footer]);

        }
    }


}
