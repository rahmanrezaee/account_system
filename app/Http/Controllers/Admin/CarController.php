<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    //
    public function index($id = null)
    {
        if (isset($id) && $id>0){
            $cars = Car::paginate($id);
        }else{
            $cars = Car::paginate(5);
        }
        return view('car.index',compact('cars'))->with(['panel_title' => 'لیست عواید موتر' ,'route' => route('car.list')]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))

            return view('car.form')->with('panel_title','اضافه کردن عواید موتر');

        else {
            $validator = Validator::make(Input::all(), [
                'car_amount' => 'required',
                'car_date' => 'required',
                'car_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $car = new Car();
                $car->revenue_amount = $request->get('car_amount');
                $car->date = $request->get('car_date');
                $car->description = $request->get('car_description');
                $car->save();
                return array(
                    'content' => 'content',
                    'url' => route('car.list')
                );

            }
        }
    }

    public function update(Request $request, $id)
    {
        $car = Car::find($id);
        if ($request->isMethod('get'))

            return view('car.form', compact('car'))->with('panel_title','ویرایش کردن عواید موتر');

        else {
            $validator = Validator::make(Input::all(), [
                'car_amount' => 'required',
                'car_date' => 'required',
                'car_description' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $car->revenue_amount = $request->get('car_amount');
                $car->date = $request->get('car_date');
                $car->description = $request->get('car_description');
                $car->update();
                return array(
                    'content' => 'content',
                    'url' => route('car.list')
                );

            }
        }
    }

    public function delete($id)
    {
        $car = Car::find($id);
        $car->delete();
        return redirect()->route('car.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
    }

    public function search($id)
    {
        $data = \DB::table('car_revenue')
            ->select('car_revenue.car_revenue_id','car_revenue.revenue_amount','car_revenue.date','car_revenue.description')
            ->where('car_revenue.car_revenue_id', 'LIKE', "%$id%")
            ->orWhere('car_revenue.revenue_amount', 'LIKE', "%$id%")
            ->orWhere('car_revenue.date', 'LIKE', "%$id%")
            ->orWhere('car_revenue.description', 'LIKE', "%$id%")
            ->get();


        if (count($data)>0){
            return response(array(
                'data'=>$data
            ));
        }
    }
}
