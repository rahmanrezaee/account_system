<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Currency;
use App\Models\Store;
use App\Models\StoreMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class StoreMoneyController extends Controller
{
    //
    public function index($id = null)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0){
            if (isset($id) && $id>0){
                $moneyStores=StoreMoney::where('status', '<', 1)->paginate($id);

            }else{

                $moneyStores=StoreMoney::where('status', '<', 1)->paginate(5);
            }
        } else {
            if (isset($id) && $id>0){
                $moneyStores=StoreMoney::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status', '<', 1)
                    ->paginate($id);
            }else{
                $moneyStores=StoreMoney::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status', '<', 1)
                    ->paginate(5);
            }
        }
        return view('money_store.index', compact('moneyStores'))->with(['panel_title' => 'لیست موجودی ذخیره شده در حساب ها', "route" => route('money_store.list')]);
    }


    public function create(Request $request){

        if ($request->isMethod('get'))
        {
            $agencies = Agency::all();
            $currencies = Currency::all();
            return view('money_store.form',compact('currencies','agencies'))->with('panel_title','اضافه کردن حساب');
        }
        else {
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
                if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
                {
                    $moneyStore->agency_id = $request->input("agency_id");
                } else {
                    $moneyStore->agency_id = \Auth::user()->agency_id;
                }
                $moneyStore->save();
                return array(
                    'content' => 'content',
                    'url' => route('money_exchange.list')
                );
            }
        }
    }


    public function update(Request $request, $id)
    {

        $moneyStore = StoreMoney::find($id);
        if($request->isMethod('get'))
        {
            $agencies = Agency::all();
            $currencies = Currency::all();
            return view('money_store.form',compact('moneyStore','currencies','agencies'))->with(['panel_title'=>'ویرایش حساب']);

        }
        else {
            $validator = Validator::make(Input::all(), [
                    'account_type' => 'required',
                    'amount' => 'required',
                    'currency_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $moneyStore->name = Input::get('account_type');
            $moneyStore->money_amount = Input::get('amount');
            $moneyStore->currency_id = Input::get('currency_id');

            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
            {
                $moneyStore->agency_id = $request->input("agency_id");
            } else {
                $moneyStore->agency_id = \Auth::user()->agency_id;
            }
            $moneyStore->update();
            return array(
                'content' => 'content',
                'url' => route('money_exchange.list')
            );

//            Session::put('msg_status', 'fkjdkfgjdlgjdlkgjdkgjdl');
        }
    }

    public function delete($id)
    {
        $moneyStore = StoreMoney::find($id);
        $moneyStore->status = 1;
        $moneyStore->delete();
        return redirect()->route('money_exchange.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');

    }
}
