<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Morilog\Jalali\Jalalian;

class HomeController extends Controller
{

    public function index(Request $request)
    {

        $user = Auth::user();
        Session::put('user_info', $user);
        Session::put('user_id', $user->user_id);



            $year = Jalalian::fromCarbon(Carbon::now())->getYear();

            $arr = array();
            $arr2 = array();
            for ($i = 1; $i <= 9; $i++) {

                $arr[] = \DB::table("buy_factor")->where("buy_date", "like", $year . "-0" . $i . "%")->sum("total_payment");
                $arr2[] = \DB::table("expense")->where("pay_date", "like", $year . "-0" . $i . "%")->sum("amount");

            }
            //it is for student payment
            $arr[] = \DB::table("buy_factor")->where("buy_date", "like", $year . "-10%")->sum("total_payment");
            $arr[] = \DB::table("buy_factor")->where("buy_date", "like", $year . "-11%")->sum("total_payment");
            $arr[] = \DB::table("buy_factor")->where("buy_date", "like", $year . "-12%")->sum("total_payment");

            $income = array();


            for ($i = 0; $i < 12; $i++) {


                $sum_of_sale_factor = \DB::table('sale_factor')
                    ->whereRaw('MONTH(sale_date) = ' . $i)
                    ->sum('total_price');

                $sum_of_expense = \DB::table('expense')
                    ->whereRaw('MONTH(pay_date) = ' . $i)
                    ->sum('amount');

                $sum_of_salary = \DB::table('salary_payment')
                    ->whereRaw('MONTH(payment_date) = ' . $i)
                    ->sum('payment_amount');

               /* $sum_of_total_payment = \DB::table('total_payment')
                    ->whereRaw("MONTH(date)=" . $i)
                    ->sum('total_payment');*/

                $income[] = 100000 - ($sum_of_sale_factor + $sum_of_expense + $sum_of_salary);

            }


            //it is for expense data chart
            $arr2[] = \DB::table("expense")->where("pay_date", "like", $year . "-10%")->sum("amount");
            $arr2[] = \DB::table("expense")->where("pay_date", "like", $year . "-11%")->sum("amount");
            $arr2[] = \DB::table("expense")->where("pay_date", "like", $year . "-12%")->sum("amount");


            $ownerInfo = \DB::table('owner')->select('full_name', 'percentage')
                ->where('status', '!=', 1)->get();


            $lable_precentage = array();
            $data_precentage = array();

            foreach ($ownerInfo as $info) {

                $lable_precentage[] = $info->full_name;
                $data_precentage[] = $info->percentage;
            }


            return view('dashboard.home', compact("arr", "arr2", "lable_precentage", "data_precentage", "income"));
        }



}
