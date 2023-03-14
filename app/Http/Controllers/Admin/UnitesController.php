<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\Unit;


class UnitesController extends Controller
{
    public function index($id = null)
    {
        if (isset($id) && $id>0){


            $units = Unit::where('status', '<', 1)->paginate($id);

        }else{

            $units = Unit::where('status', '<', 1)->paginate(5);

        }

        return view('unit.index', compact('units'))->with(['panel_title' => 'لیست واحد ها','route' =>route('unit.list')]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))

            return view('unit.form')->with(['panel_title' => 'ایجاد واحد جدید']);
        else {
            $validator = Validator::make(Input::all(), [
                'unit_name' => 'required',

            ],
                [
                    'unit_name.required' => 'وارد کردن نام واحد  الزامی میباشد!',
                    'unit_number.required' => 'وارد کردن تعداد  الزامی میباشد!',]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $unit = new Unit();
            $unit->unit_name = Input::get('unit_name');
            $unit->unit_quantity = Input::get('unit_number');

            $unit->save();
            return array(

                'content' => 'content',
                'url' => route('unit.create')
            );

        }

    }
    public function createMenual(Request $request)
    {
            $validator = Validator::make(Input::all(), [
                'unit_name' => 'required',

            ],
                [
                    'unit_name.required' => 'وارد کردن نام واحد  الزامی میباشد!',
                    'unit_number.required' => 'وارد کردن تعداد  الزامی میباشد!',]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $unit = new Unit();
            $unit->unit_name = Input::get('unit_name');
            $unit->unit_quantity = Input::get('unit_number');

            $unit->save();
           return \Response::json($unit);


    }

    public function update(Request $request, $id)
    {

        $unit = Unit::find($id);
        if ($request->isMethod('get'))

            return view('unit.form', compact('unit'))->with(['panel_title' => 'ویرایش واحد']);
        else {
            $validator = Validator::make(Input::all(), [
                'unit_name' => 'required'

            ], [
                    'unit_name.required' => 'وارد کردن نام الزامی میباشد!',

                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }


            $unit->unit_name = Input::get('unit_name');
            $unit->unit_quantity = Input::get('unit_number');
            $unit->save();
            return array(

                'content' => 'content',
                'url' => route('unit.list')
            );

        }


    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            $unit = Unit::find($id)->where('unit_id', $id)->update(['status' => 1]);
            return redirect()->route("unit.list")->with('success', 'عمل حذف باموفقیت انجام شد');
        }
    }

    public function search($id)
    {
        $data = \DB::table('product_unit')
            ->select('product_unit.unit_id', 'product_unit.unit_name')
            ->where('product_unit.unit_id', 'LIKE', "%$id%")
            ->orWhere('product_unit.unit_name', 'LIKE', "%$id%")
            ->where('product_unit.status', '!=', 1)
            ->get();

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }
}
