<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index()
    {

        $currencies = Currency::where('status', '<', 1)->get();
        return view('Currency_setting.index', compact('currencies'))->with(['panel_title' => 'لیست واحد پولی ها']);

    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))

            return view('Currency_setting.form')->with(['panel_title' => 'ایجاد واحد جدید']);

        else {
            $validator = Validator::make(Input::all(), [
                'currency_name' => 'required',
                'symbol' => 'required',
            ],
                [
                    'currency_name.required' => 'وارد کردن نام ارز  الزامی میباشد!',
                    'currency_name.symbol' => 'وارد کردن سمبول ارز  الزامی میباشد!'
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
            $currency = new Currency();
            $currency->currency_name = Input::get('currency_name');
            $currency->symbol = Input::get('symbol');
            $currency->save();
            return array(

                'content' => 'content',
                'url' => route('currency.list')
            );

        }

    }

    public function update(Request $request, $id)
    {

        $currency = Currency::find($id);
        if ($request->isMethod('get'))

            return view('Currency_setting.form', compact('currency'))->with(['panel_title' => 'ویرایش ارز ']);
        else {

            $validator = Validator::make(Input::all(), [
                'currency_name' => 'required',
            ],
                [
                    'currency_name.required' => 'وارد کردن نام ارز  الزامی میباشد!',
                    'currency_name.symbol' => 'وارد کردن سمبول ارز  الزامی میباشد!'
                ]
            );

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $currency->currency_name = Input::get('currency_name');
            $currency->symbol = Input::get('symbol');
            $currency->save();

            return array(
                'content' => 'content',
                'url' => route('currency.list')
            );
        }

    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            $currency =Currency::find($id)->where('currency_id', $id)->update(['status' => 1]);
            return redirect()->route("currency.list")->with('success', 'عمل حذف باموفقیت انجام شد');
        }
    }

}
