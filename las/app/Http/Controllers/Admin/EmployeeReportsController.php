<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\MoneyStore;
use App\Models\StoreMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\SalaryPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EmployeeReportsController extends Controller
{
    public function index($id = null)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $salary = SalaryPayment::select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name  as account_name')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->where('payment_borrow', '>', 0)
                    ->where('salary_payment.status', '=', 0)
                    ->paginate($id);
            } else {
                $salary = \DB::table('salary_payment')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name as account_name')
                    ->where('payment_borrow', '>', 0)
                    ->where('salary_payment.status', '=', 0)
                    ->paginate(5);
            }
        } else {
            if (isset($id) && $id > 0) {
                $salary = SalaryPayment::select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name  as account_name')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->where('employee.agency_id', '=', Auth::user()->agency_id)
                    ->where('payment_borrow', '>', 0)
                    ->where('salary_payment.status', '=', 0)
                    ->paginate($id);
            } else {
                $salary = \DB::table('salary_payment')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name as account_name')
                    ->where('employee.agency_id', '=', Auth::user()->agency_id)
                    ->where('payment_borrow', '>', 0)
                    ->where('salary_payment.status', '=', 0)
                    ->paginate(5);
            }
        }

        return view('employeereport.index', compact('salary'))->with(['panel_title' => 'پرداخت باقیات معاش کارمندان', 'route' => route('employeereport.list')]);
    }

    public function paymented($id = null)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            if (isset($id) && $id > 0) {
                $salary = SalaryPayment::select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->where('payment_borrow', '=', 0)
                    ->where('salary_payment.status', '=', 0)
                    ->paginate($id);
            } else {
                $salary = \DB::table('salary_payment')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name as account_name')
                    ->where('salary_payment.status', '=', 0)
                    ->where('payment_borrow', '=', 0)
                    ->paginate(5);
            }
        } else {
            if (isset($id) && $id > 0) {
                $salary = SalaryPayment::select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->where('employee.agency_id', '=', Auth::user()->agency_id)
                    ->where('payment_borrow', '=', 0)
                    ->where('salary_payment.status', '=', 0)
                    ->paginate($id);
            } else {
                $salary = \DB::table('salary_payment')
                    ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                    ->join('money_store', 'money_store.store_id', '=', 'account_id')
                    ->select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name as account_name')
                    ->where('employee.agency_id', '=', Auth::user()->agency_id)
                    ->where('salary_payment.status', '=', 0)
                    ->where('payment_borrow', '=', 0)
                    ->paginate(5);
            }
        }

        return view('employeereport.paymented', compact('salary'))->with(['panel_title' => 'پرداختی  معاشات کارمندان', 'route' => route('employeereport.paymented')]);

    }

    public function paymented_update(Request $request, $id)
    {
        $salary = SalaryPayment::find($id);

        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $moneyStores = StoreMoney::all();

            $employee = Employee::select('employee_id', 'salary', 'first_name', 'last_name')
                ->where('status', '!=', 1)->get();
        } else {
            $moneyStores = StoreMoney::join('store', 'money_store.store_id', '=', 'store.money_store_id')
                ->where('store.agency_id', '=', Auth::user()->agency_id)
                ->get();

            $employee = Employee::select('employee_id', 'salary', 'first_name', 'last_name')
                ->where('agency_id', '=', Auth::user()->agency_id)
                ->where('status', '!=', 1)
                ->get();
        }
        if ($request->isMethod('get'))

            return view('employeereport.edit_paymented_form', compact('salary', 'employee', 'moneyStores'))->with(['panel_title' => 'ویرایش پرداختی معاش کارمند']);
        else {

            $validator = Validator::make(Input::all(), [

                'employee_id' => 'required',
                'payment_amount' => 'required',
                'payment_month' => 'required',
                'payment_date' => 'required'
            ]);
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {
                $salary = SalaryPayment::find($id);
                $money = StoreMoney::where('money_amount', '>=', $request->input('payment_amount'))->where('store_id', $request->get('account_id'))->get();
                if (count($money) > 0) {
                    StoreMoney::where('store_id', $salary->account_id)->increment('money_amount', $salary->payment_amount);
                    StoreMoney::where('store_id', Input::get('account_id'))->decrement('money_amount', Input::get('payment_amount'));

                    $salary->payment_borrow = (Input::get('salary') - Input::get('payment_amount'));
                    $salary->payment_date = $request->get('payment_date');
                    $salary->payment_month = $request->get('payment_month');
                    $salary->payment_amount = $request->get('payment_amount');
                    $salary->employee_id = $request->get('employee_id');
                    $salary->user_id = Session::get('user_id');

                    $salary->update();
                } else {
                    return array(
                        'fail' => true,
                        'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                    );
                }
                return array(
                    'content' => 'content',
                    'url' => route('employeereport.paymented')
                );
            }
        }
    }


    public function getSalary($id)
    {
        $employee = \DB::table("employee")
            ->select("employee_id", "salary", 'currency_id')
            ->where("employee_id", $id)->first();

        $res = [
            'sallary' => $employee->salary,
            'currency_name' => Currency::find($employee->currency_id)->currency_name,
            'currency_id' => $employee->currency_id
        ];


        return Response()->json($res);
    }

    public function empGetBorrow($id)
    {
        $borrow = \DB::table("money_store")
            ->sum('payment_amount')
            ->where("employee_id", $id)
            ->where('payment_type', '=', 'برداشت')
            ->where('payment_status', '=', 'پرداخت نشده');
        return ($borrow);
    }

    public function create(Request $request)
    {


        if ($request->isMethod('get')) {

            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                $moneyStores = StoreMoney::all();
            } else {
                $moneyStores = StoreMoney::where('agency_id', '=', Auth::user()->agency_id)->get();
            }

            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
                $employee = Employee::select('employee_id', 'salary', 'first_name', 'last_name')
                    ->where('status', '==', 0)
                    ->get();
            } else {
                $employee = Employee::select('employee_id', 'salary', 'first_name', 'last_name', 'currency_id')
                    ->where('agency_id', '=', Auth::user()->agency_id)
                    ->where('status', '==', 0)
                    ->get();
            }

            return view('employeereport.form', compact('employee', 'moneyStores'))->with('panel_title', 'پرداخت معاش کارمندان');

        } else {

            //$empSalary = Input::get('salary');
            $validator = Validator::make(Input::all(), array(

                'employee_id' => 'required',
                'payment_amount' => 'required',
                'payment_month' => 'required',
                'payment_date' => 'required |min:1'

            ));
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $moneyStore = MoneyStore::find(Input::get('account_id'));


                $sp = new SalaryPayment();
                $sp->payment_date = Input::get('payment_date');
                $sp->payment_month = Input::get('payment_month');
                $sp->payment_amount = Input::get('payment_amount');
                $sp->payment_borrow = (Input::get('salary') - Input::get('payment_amount'));
                $sp->employee_id = Input::get('employee_id');
                $sp->account_id = Input::get('account_id');
                $sp->user_id = Session::get('user_id');

                $sp->currency_main_id = Employee::find(Input::get('employee_id'))->currency_id;
                $sp->currency_rate = Input::get('currency_rate');
                $sp->currency_id = Input::get('currency_id');


                $money = $moneyStore->money_amount;
                $currentMoney = $sp->currency_rate * $sp->payment_amount;

                if ($money > $currentMoney) {
                    $moneyStore->decrement('money_amount', $currentMoney);
                    $sp->save();
                } else {
                    return array(
                        'fail' => true,
                        'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                    );
                }
                return array(
                    'content' => 'content',
                    'url' => route('employeereport.list')
                );
            }
        }
    }


    public function update(Request $request, $id)
    {
        $salary = SalaryPayment::find($id);

        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $moneyStores = StoreMoney::all();

            $employee = Employee::select('employee_id', 'salary', 'first_name', 'last_name')
                ->where('status', '!=', 1)
                ->get();


        } else {
            $moneyStores = StoreMoney::where('agency_id', '=', Auth::user()->agency_id)->get();
            $employee = Employee::select('employee_id', 'salary', 'first_name', 'last_name')
                ->where('agency_id', '=', Auth::user()->agency_id)
                ->where('status', '!=', 1)
                ->get();
        }




        if ($request->isMethod('get'))

            return view('employeereport.form', compact('salary', 'employee', 'moneyStores'))->with(['panel_title' => 'ویرایش پرداخت معاش کارمند']);

        else {



            $validator = Validator::make(Input::all(), [

                'employee_id' => 'required',
                'payment_amount' => 'required',
                'payment_month' => 'required',
                'payment_date' => 'required'
            ]);
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {


                //find salary employee
                $salary = SalaryPayment::find($id);

                //find the money store
                $moneyStore = MoneyStore::find(Input::get('account_id'));

                $currentMoney = $salary->currency_rate * $salary->payment_amount;

                // set default money store
                $moneyStore->increment('money_amount',$currentMoney);

                $currentPayment =  $request->get('payment_amount') *  $request->get('currency_rate');

                $money = $moneyStore->money_amount;

                if ($money > $currentPayment) {

                    $moneyStore->decrement('money_amount', $currentPayment);
                    $salary->payment_date = $request->get('payment_date');
                    $salary->payment_month = $request->get('payment_month');
                    $salary->payment_amount = $request->get('payment_amount');
                    $salary->payment_borrow = (Input::get('salary') - $salary->payment_amount);
                    $salary->employee_id = $request->get('employee_id');
                    $salary->account_id = $request->get('account_id');
                    $salary->user_id = Session::get('user_id');
                    $salary->currency_main_id = Employee::find(Input::get('employee_id'))->currency_id;
                    $salary->currency_rate = Input::get('currency_rate');
                    $salary->currency_id = Input::get('currency_id');
                    $salary->save();

                } else {

                    $moneyStore->decrement('money_amount', $currentMoney);

                    return array(
                        'fail' => true,
                        'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                    );
                }

                return array(
                    'content' => 'content',
                    'url' => route('employeereport.list')
                );

            }
        }
    }


    public function payment(Request $request, $id)
    {
        $salary = SalaryPayment::find($id);

        $employee = Employee::find($salary->employee_id);
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $moneyStores = StoreMoney::all();
        } else {
            $moneyStores = StoreMoney::where('agency_id', '=', Auth::user()->agency_id)->get();
        }


        if ($request->isMethod('get'))
            return view('employeereport.payment_form', compact('salary', 'employee', 'moneyStores'))->with(['panel_title' => 'پرداخت باقی معاش کارمند']);
        else {


            $validator = Validator::make(Input::all(), [
                'id' => 'required',
                'payment_amount' => 'required|min:1',
                'payment_month' => 'required',
                'account_id' => 'required|not_in:-1',

                'payment_date' => 'required'
            ]);

            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            if (ctype_digit($request->get('id'))) {

                $payment = Input::get('payment_amount');

                if ($payment <= $salary->payment_borrow) {

                    $sp = SalaryPayment::find($id);
                    $moneyStore = MoneyStore::find(Input::get('account_id'));

                    $sp->payment_date = $request->get('payment_date');
                    $sp->payment_month = $request->get('payment_month');
                    $sp->currency_rate = $request->get('currency_rate');
                    $sp->currency_id = $request->get('currency_id');
                    $sp->account_id = $request->get('account_id');

                    $sp->payment_amount = ($salary->payment_amount + $payment);
                    $sp->payment_borrow = ($salary->payment_borrow - $payment);

                    $sp->user_id = Session::get('user_id');


                    $money = $moneyStore->money_amount;
                    $currentMoney = $sp->currency_rate * $payment;

                    if ($money > $currentMoney) {

                        $moneyStore->decrement('money_amount', $currentMoney);
                        $sp->save();


                    } else {
                        return array(
                            'fail' => true,
                            'errors' => ['شما در حساب تان به اندازه کافی پول ندارید']
                        );
                    }

                    return array(
                        'content' => 'content',
                        'url' => route('employeereport.list')
                    );
                }
            }

        }
    }


    public function getdata(Request $request)
    {
        $em = SalaryPayment::select('payment_amount', 'payment_borrow', 'payment_date')->where('payment_date', $request->id)
            ->where('employee_id', $request->employeeId)->get();
        return response()->json($em);
    }


    public function empSalaryPayPercentage()
    {


        $salary = Employee::selectRaw('employee.employee_id, salary_type, salary, first_name, last_name')
            ->where('salary_type', '=', 'فیصدی')
            ->where('employee.status', '!=', 1)
            ->Join('class', 'class.employee_id', '=', 'employee.employee_id')
            ->Join('student_class', 'student_class.class_id', '=', 'class.class_id')
            ->Join('subject', 'subject.subject_id', '=', 'class.subject_id')
            ->get();

        return view('employeereport.empSalaryPayPercentage', compact('salary'))->with(['panel_title' => 'پرداخت باقیات معاش کارمندان']);

    }

    public function delete($id)
    {
        $salary = SalaryPayment::find($id);
        $salary->status = 1;
        $salary->update();
        return redirect()->route("employeereport.list")->with('success', 'عملیه حذف با موفقیتت انجام شد');
    }

    public function search($id)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0) {
            $data = \DB::table('salary_payment')
                ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                ->join('money_store', 'money_store.store_id', '=', 'account_id')
                ->select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name')
                ->where('payment_borrow', '>', 0)
                ->where('payment_id', 'LIKE', "%$id%")
                ->orWhere('payment_amount', 'LIKE', "%$id%")
                ->orWhere('payment_borrow', 'LIKE', "%$id%")
                ->orWhere('payment_date', 'LIKE', "%$id%")
                ->orWhere('payment_month', 'LIKE', "%$id%")
                ->orWhere('salary', 'LIKE', "%$id%")
                ->orWhere('first_name', 'LIKE', "%$id%")
                ->orWhere('last_name', 'LIKE', "%$id%")
                ->orWhere('money_store.name', 'LIKE', "%$id%")
                ->where("salary_payment.status", "!=", "1")
                ->get();
        } else {
            $data = \DB::table('salary_payment')
                ->Join('employee', 'employee.employee_id', '=', 'salary_payment.employee_id')
                ->join('money_store', 'money_store.store_id', '=', 'account_id')
                ->select('payment_id', 'payment_amount', 'payment_borrow', 'payment_date', 'payment_month', 'salary', 'first_name', 'last_name', 'money_store.name')
                ->where('payment_borrow', '>', 0)
                ->where('employee.agency_id', '=', Auth::user()->agency_id)
                ->where(function ($q) use ($id) {
                    $q->where('payment_id', 'LIKE', "%$id%")
                        ->orWhere('payment_amount', 'LIKE', "%$id%")
                        ->orWhere('payment_borrow', 'LIKE', "%$id%")
                        ->orWhere('payment_date', 'LIKE', "%$id%")
                        ->orWhere('payment_month', 'LIKE', "%$id%")
                        ->orWhere('salary', 'LIKE', "%$id%")
                        ->orWhere('first_name', 'LIKE', "%$id%")
                        ->orWhere('last_name', 'LIKE', "%$id%")
                        ->orWhere('money_store.name', 'LIKE', "%$id%");
                })
                ->where("salary_payment.status", "!=", "1")
                ->get();
        }

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }

}
