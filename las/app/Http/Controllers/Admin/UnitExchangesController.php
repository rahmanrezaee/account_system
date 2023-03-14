<?php

namespace App\Http\Controllers\Admin;

use App\Models\Unit;
use App\Models\UnitExchange;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class UnitExchangesController extends Controller
{
    public function index()
    {



        $unitExchanges = UnitExchange::all();

        $units = Unit::where('status', '<', 1)->get();


        return view('product.ExchangeUnit', compact('unitExchanges', 'units'))->with(['panel_title' => 'لیست تبدیل واحدات ها']);

    }

    public function createAndUpdate(Request $request)
    {

        $validator = Validator::make(Input::all(), [
                'main_unit' => 'required',
                'main_quentity' => 'required',
                'relate_unit_id' => 'required',
                'quentity' => 'required',
            ]

        );

        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        }


        $inputs = $request->input();



        UnitExchange::truncate();



        for ($row = 0; $row <count($inputs['main_unit']); $row++) {

            $r = new UnitExchange();
            $r->main_unit_id = $inputs['main_unit'][$row];
            $r->main_quentity = $inputs['main_quentity'][$row];
            $r->relate_unit_id = $inputs['relate_unit_id'][$row];
            $r->quentity = $inputs['quentity'][$row];
            $r->save();

        }


        return array(

            'content' => 'content',
            'url' => route('getUnitExchangers.list')
        );


    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            $currency = Currency::find($id)->where('currency_id', $id)->update(['status' => 1]);
            return redirect()->route("currency.list")->with('success', 'عمل حذف باموفقیت انجام شد');
        }
    }

}
