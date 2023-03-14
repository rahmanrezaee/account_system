<?php

namespace App\Http\Controllers\Admin;

use App\Models\AddMoney;
use App\Models\StoreMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;


class AddMoneyController extends Controller
{
    public function index($id = null)
    {
        if (isset($id) && $id>0){
            $addMoney = AddMoney::where('status','=', 0)->paginate($id);
        }else{
            $addMoney = AddMoney::where('status','=', 0)->paginate(5);
        }
        return view('add_money.index', compact('addMoney'))->with(['panel_title' => 'لیست اضافه کردن پول به صندوق', 'route' => route('add_money.list')]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))
        {
            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
            {
                $moneyStores = StoreMoney::all();
            } else {
                $moneyStores = StoreMoney::where('agency_id','=',\Auth::user()->agency_id)
                    ->where('status','=',0)
                    ->get();
            }
            return view('add_money.form', compact('moneyStores'))->with('panel_title','ثبت پول به صندوق');
        }
        else {
            $validator = \Validator::make(Input::all(), [
                'add_money_amount' => 'required',
                'account_id' => 'required',
                'add_money_date' => 'required',
                'add_money_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $addMoney = new AddMoney();
                $addMoney->money_amount = $request->get('add_money_amount');
                $addMoney->account_type = $request->get('account_id');
                $addMoney->date = $request->get('add_money_date');
                $addMoney->description = $request->get('add_money_description');

                StoreMoney::where('store_id', $addMoney->account_type)->increment('money_amount', $request->get('add_money_amount'));
                $addMoney->save();


                return array(
                    'content' => 'content',
                    'url' => route('add_money.list')
                );

            }
        }
    }

//
    public function update(Request $request, $id)
    {
        $addMoney = AddMoney::find($id);
        if ($request->isMethod('get')) {
            $moneyStores = StoreMoney::all();
            return view('add_money.form', compact('moneyStores', 'addMoney'))->with('panel_title', 'ویرایش پول ذخیره شده');
        }
        else {
            $validator = \Validator::make(Input::all(), [
                'add_money_amount' => 'required',
                'account_id' => 'required',
                'add_money_date' => 'required',
                'add_money_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $addMoney = AddMoney::find($id);
                StoreMoney::where('store_id', $addMoney->account_type)->decrement('money_amount', $addMoney->money_amount);
                StoreMoney::where('store_id', Input::get('account_id'))->increment('money_amount', Input::get('add_money_amount'));

                $addMoney->money_amount = $request->get('add_money_amount');
                $addMoney->account_type = $request->get('account_id');
                $addMoney->date = $request->get('add_money_date');
                $addMoney->description = $request->get('add_money_description');
                $addMoney->update();

                return array(
                    'content' => 'content',
                    'url' => route('add_money.list')
                );
            }
        }

    }

    public function delete($id)
    {
        $addMoney = AddMoney::find($id);
        $addMoney->status = 1;
        $addMoney->update();

        return redirect()->route('add_money.list')->with('success','با موفقیت حذف شد ');
    }

    public function search($id)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
        {
            $data = \DB::table('add_money')
                ->join('money_store','add_money.account_type','=','money_store.store_id')
                ->select('add_money.add_money_id','add_money.money_amount','add_money.date','add_money.description','money_store.name')
                ->where('add_money.add_money_id', 'LIKE', "%$id%")
                ->orWhere('add_money.money_amount', 'LIKE', "%$id%")
                ->orWhere('add_money.date', 'LIKE', "%$id%")
                ->orWhere('money_store.name', 'LIKE', "%$id%")
                ->orWhere('add_money.description', 'LIKE', "%$id%")
                ->where('add_money.status','=',0)
                ->get();
        } else {
            $data = \DB::table('add_money')
                ->join('money_store','add_money.account_type','=','money_store.store_id')
                ->select('add_money.add_money_id','add_money.money_amount','add_money.date','add_money.description','money_store.name')
                ->where('money_store.agency_id','=',\Auth::user()->agency_id)
                ->where(function($q) use ($id) {
                    $q->where('add_money.add_money_id', 'LIKE', "%$id%")
                        ->orWhere('add_money.money_amount', 'LIKE', "%$id%")
                        ->orWhere('add_money.date', 'LIKE', "%$id%")
                        ->orWhere('money_store.name', 'LIKE', "%$id%")
                        ->orWhere('add_money.description', 'LIKE', "%$id%");
                })
                ->where('add_money.status','=',0)
                ->get();
        }

        if (count($data)>0){
            return response(array(
                'data'=>$data
            ));
        }
    }
}
