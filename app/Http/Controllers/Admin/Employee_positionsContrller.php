<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\employee_position;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class Employee_positionsContrller extends Controller
{
    public function index($id = null)
    {
        if (isset($id) && $id>0){
            $users = employee_position::where('status', '<', 1)->paginate($id);
        }else{
            $users = employee_position::where('status', '<', 1)->paginate(5);
        }

        return view('position.index',compact('users'))->with(['panel_title'=>'لیست مقام ها', 'route' => route('position.list')]);

    }

    public function create(Request $request)
    {
        if($request->isMethod('get'))

            return view('position.form')->with(['panel_title'=>'ایجاد مقام جدید']);
        else {
            $validator = Validator::make(Input::all(), [
                'position_name' => 'required'

            ],[
                    'position_name.required' => 'وارد کردن مقام  الزامی میباشد!',

                ]
                );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
        }
        $name=$request->input('position_name');

         $recourd=DB::table('employee_position')->where('position_name',$name)->doesntExist();
         if ($recourd){
             $position = new employee_position();
             $position->position_name = Input::get('position_name');

             $position->save();
             return array(

                 'content' => 'content',
                 'url' => route('position.list')
             );
             Session::put('msg_status', true);
         }
        return array(

            'content' => 'content',
            'url' => route('position.list')
        );

        Session::put('msg_status', 'jfskdjfksjfksjflskfjsk');


    }

    public function update(Request $request, $id)
    {
      
        $position=employee_position::find($id);
        if($request->isMethod('get'))

            return view('position.form',compact('position'))->with(['panel_title'=>'ویرایش  مقام']);
        else {
            $validator = Validator::make(Input::all(), [
                'position_name' => 'required'
                
            ],[
                    'position_name.required' => 'وارد کردن مقام  الزامی میباشد!',

                    ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

           
            $position->position_name=Input::get('position_name');

            $position->save();

            return array(

                'content' => 'content',
                'url' => route('position.list')
            );

            Session::put('msg_status', 'jfskdjfksjfksjflskfjsk');
        }


    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            $user = employee_position::find($id)->where('position_id', $id)->update(['status' => 1]);
            return redirect()->route('position.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');


        }
    }

    public function search($id)
    {
        $data = \DB::table('employee_position')
            ->select("position_id", "position_name")
            ->where('position_id', 'LIKE', "%$id%")
            ->orWhere('position_name', 'LIKE', "%$id%")
            ->where("employee_position.status", "!=", "1")
            ->get();

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }
}
