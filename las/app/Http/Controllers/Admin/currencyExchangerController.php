<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\CurrencyExchanger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class currencyExchangerController extends Controller
{

    public function index()
    {


        $mainCurrencyId = get_options('mainCurrency')->value('option_value');
        $mainCurrency = Currency::where('status', '<', 1)->where('currency_id', $mainCurrencyId)->first();
        $currencyExchanger = CurrencyExchanger::where('status', '<', 1)->get();
        $currencies = Currency::where('status', '<', 1)->get();

        return view('currencyExchanger.index', compact('currencyExchanger', 'currencies', 'mainCurrency'))->with(['panel_title' => 'لیست تبدیل واحدات پولی ها']);

    }

    public function createAndUpdate(Request $request)
    {


        if (($request->input("main_currency"))) {

            $a = 0;

//            foreach ($request->input("other_currency") as $other) {
//                foreach ($request->input("main_currency") as $main) {
//
//                    if ($main == $other){
//                        $a++;
//                    }
//
//                }
//
//            }
//            if ($a != 1) {
//
//                return array(
//                    'fail' => true,
//                    'errors' => ["خطا" => "ارز تان دوبار تکرار شده"]
//                );
//
//            }
//            dd("hell");

        }


        $validator = Validator::make(Input::all(), [
                'main_currency' => 'required',
                'other_currency' => 'required',
                'money_amount' => 'required',
                'exchange_rate' => 'required',
            ]

        );

        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        }


        $inputs = $request->input();



        CurrencyExchanger::truncate();


//        dd($inputs);

        for ($row = 0; $row <count($inputs['main_currency']); $row++) {

            $r = new CurrencyExchanger();
            $r->main_currency_id = $inputs['main_currency'][$row];
            $r->other_currency_id = $inputs['other_currency'][$row];
            $r->money_amount = $inputs['money_amount'][$row];
            $r->exchange_rate = $inputs['exchange_rate'][$row];
            $r->save();

        }


        return array(

            'content' => 'content',
            'url' => route('currencyExchanger.list')
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
