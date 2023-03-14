<?php

namespace App\Http\Controllers\Admin;

use App\Models\Revenue;
use App\Models\StoreMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class RevenueController extends Controller
{
    public function index()
    {
        $revenues = Revenue::all();
        return view('revenue.index', compact('revenues'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))
        {
            $moneyStores = StoreMoney::all();
            return view('revenue.form', compact('moneyStores'))->with('panel_title','سرمایه اولیه');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'account_type' => 'required',
                'money_amount' => 'required',
                'revenue_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $revenue = new Revenue();
                $revenue->account_type = $request->get('account_type');
                $revenue->money_amount = $request->get('money_amount');
                $revenue->description = $request->get('revenue_description');
                $revenue->save();


                return array(
                    'content' => 'content',
                    'url' => route('revenue.list')
                );

            }
        }
    }


    public function update(Request $request, $id)
    {
        $revenue = Revenue::find($id);
        if ($request->isMethod('get'))
        {
            $moneyStores = StoreMoney::all();
            return view('revenue.form', compact('revenue','moneyStores'))->with('panel_title','ویرایش کردن سرمایه اولیه');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'account_type' => 'required',
                'money_amount' => 'required',
                'revenue_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $revenue->account_type = $request->get('account_type');
                $revenue->money_amount = $request->get('money_amount');
                $revenue->description = $request->get('revenue_description');
                $revenue->update();
                return array(
                    'content' => 'content',
                    'url' => route('revenue.list')
                );

            }
        }
    }

    public function delete($id)
    {
        $revenue = Revenue::find($id);
        $revenue->delete();
        return redirect()->route('revenue.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
    }
}
