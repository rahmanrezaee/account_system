<?php

namespace App\Http\Controllers\Admin;

use App\Models\salary_payment;
use Illuminate\Http\Request;
use \App\Models\Employee;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Model\employee_position;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Symfony\Component\VarDumper\Cloner\Data;

class ReportController extends Controller
{
    public function index(Request $request)
    {


        $salary = salary_payment::select('payment_id', 'payment_amount', 'salary', 'payment_barrow', 'month', 'first_name', 'employee_id')->Join('employee', 'employe_id', '=', 'employee_id')->where('payment_barrow', '!=', 0)->where('salary_payment.status', '=', 0)->get();

        return view('employeereport.index
        ', compact('salary'))->with(['panel_title' => 'لیست بدهکار ی مشتریان ']);

    }

    public function create(Request $request)
    {

        $employee = Employee::all();

        if ($request->isMethod('get'))


            return view('employeereport.form', compact('employee'));
        else {
            $validator = Validator::make(Input::all(), [
                'payment_amount' => 'required',
                'payment_date' => 'required'


            ], [
                    'payment_amount.required' => 'وارد کردن  مقدار پرداخت الزامی میباشد!',

                    'payment_date.required' => 'وارد کردن  تاریخ الزامی میباشد!',


                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $sallary = new salary_payment();

            $id = $request->input('employee_id');
            $employeesalry = DB::table('employee')->where('employee_id', $id)->value('salary');
            $month = $sallary->month = Input::get('month');
            $record = DB::table('salary_payment')
                ->where('employe_id', $id)
                ->where('month', $month)->exists();
            $payment = $sallary->payment_amount = Input::get('payment_amount');
            if ($payment > $employeesalry) {

            } elseif ($record) {


            } else {

                $sallary->payment_barrow = ($employeesalry - $payment);
                $sallary->payment_amount = Input::get('payment_amount');


                $sallary->employe_id = Input::get('employee_id');
                $sallary->month = Input::get('month');
                $sallary->payment_date = Input::get('payment_date');

                $sallary->save();
                return array(

                    'content' => 'content',
                    'url' => route('employeereport.list')
                );
                Session::put('msg_status', true);


            }


        }


    }

    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        $salary = salary_payment::find($id);
        if ($request->isMethod('get'))

            return view('employeereport.form', compact('salary', 'employee'))->with(['panel_title' => '']);

        else {
            $validator = Validator::make(Input::all(), [
                'payment_amount' => 'required',
                'month_id' => 'required',
                'date' => 'required'


            ], [
                    'payment_amount.required' => 'وارد کردن  مقدار پرداخت الزامی میباشد!',


                ]
            );
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }


            $salary->month = Input::get('month_id');
            $salary->payment_date = Input::get('date');
            $paymentbarrow = $request->Input('paymentbarrow');
            $employeeid = $request->Input('employeeid');
            $bar = $salary->payment_amount = Input::get('payment_amount');


            if ($paymentbarrow >= $bar) {

                $salary->decrement('payment_barrow', $bar);
                $salary->increment('payment_amount', $bar);


                $salary->save();
                return array(

                    'content' => 'content',
                    'url' => route('employeereport.list')
                );
                Session::put('msg_status', true);

            }

        }
    }

    public function delete($id)
    {


        if ($id && ctype_digit($id)) {
            $customer = salary_payment::find($id)->where('payment_id', $id)->update(['status' => 1]);
            return redirect()->route('employeereport.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');


        }


    }

    public function getdata(Request $request)
    {
        $em = salary_payment::select('payment_amount', 'payment_barrow', 'month', 'payment_id')->where('month', $request->id)
            ->where('employe_id', $request->employeeId)->get();
        return response()->json($em);
    }


    public function getemployeeinfo(Request $request)
    {
        $employeeinf = Employee::select('salary', 'first_name', 'last_name')->where('employee_id', $request->id)->get();
        return response()->json($employeeinf);

    }


}



