<?php

namespace App\Http\Controllers\Admin;

use App\Models\FirstEquipmentMoney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class FirstEquipmentMoneyController extends Controller
{
    public function index($id = null)
    {
        if (isset($id) && $id>0){
            $first_equipments = FirstEquipmentMoney::paginate($id);
        }else{
            $first_equipments = FirstEquipmentMoney::paginate(5);
        }

        return view('first_equipment_money.index', compact('first_equipments'))->with(['panel_title' => 'لیست مصارف تجهیزات', 'route' => route('first_equipment_money.list')]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))
        {
            return view('first_equipment_money.form')->with('panel_title','مصارف تجهیزات اولیه');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'money_amount' => 'required',
                'date' => 'required',
                'equipment_name' => 'required',
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $first_equipment_money = new FirstEquipmentMoney();
                $first_equipment_money->money_amount = $request->get('money_amount');
                $first_equipment_money->date = $request->get('date');
                $first_equipment_money->equipment_name = $request->get('equipment_name');
                $first_equipment_money->description = $request->get('description');
                $first_equipment_money->save();


                return array(
                    'content' => 'content',
                    'url' => route('first_equipment_money.list')
                );

            }
        }
    }


    public function update(Request $request, $id)
    {
        $first_equipment_money = FirstEquipmentMoney::find($id);
        if ($request->isMethod('get'))
        {
            return view('first_equipment_money.form', compact('first_equipment_money'))->with('panel_title','ویرایش کردن مصارف تجهیزات ');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'money_amount' => 'required',
                'date' => 'required',
                'equipment_name' => 'required',
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $first_equipment_money->money_amount = $request->get('money_amount');
                $first_equipment_money->date = $request->get('date');
                $first_equipment_money->equipment_name = $request->get('equipment_name');
                $first_equipment_money->description = $request->get('description');
                $first_equipment_money->update();
                return array(
                    'content' => 'content',
                    'url' => route('first_equipment_money.list')
                );

            }
        }
    }

    public function delete($id)
    {
        $first_equipment_money = FirstEquipmentMoney::find($id);
        $first_equipment_money->delete();
        return redirect()->route('first_equipment_money.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
    }

    public function search($id)
    {
        $data = \DB::table('first_equipment_money')
            ->select("first_money_eq_id", "equipment_name","money_amount","date","description")
            ->where('first_money_eq_id', 'LIKE', "%$id%")
            ->orWhere('equipment_name', 'LIKE', "%$id%")
            ->orWhere('money_amount', 'LIKE', "%$id%")
            ->orWhere('date', 'LIKE', "%$id%")
            ->orWhere('description', 'LIKE', "%$id%")
            ->where("first_equipment_money.status", "!=", "1")
            ->get();

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }
}
