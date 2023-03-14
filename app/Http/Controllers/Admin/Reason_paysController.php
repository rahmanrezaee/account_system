<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reason_Pay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;


class Reason_paysController extends Controller
{
    public function index($id = null)
    {
        if (isset($id) && $id>0){
            $reason_pays =Reason_Pay::where('status', '<', 1)->paginate($id);
        }else{
            $reason_pays =Reason_Pay::where('status', '<', 1)->paginate(5);
        }


        return view('reason_pay.index',compact('reason_pays'))->with(['route' => route('reason_pay.list')]);

    }

    public function create(Request $request)
    {


        if($request->isMethod('get'))


            return view('reason_pay.form');
        else {
            //regex:[A-Za-z1-9]

            $validator = Validator::make(Input::all(), [
                "title" => "required",


            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }



            $reason= new Reason_Pay();
            $reason->title=Input::get('title');
            $reason->save();
            Session::put('msg_status', true);
            return array(
                'content' => 'content',
                'url' => route('reason_pay.list')
            );
        }

    }

//    public function search(Request $request)
//    {
//
//
//        return Reason_Pay:: select('title')->where('title', 'LIKE', '%'.$request->q.'%')->get();
//
//    }



    public function get_data()
    {
         $reasons=Reason_Pay::all();
         return view('management.reason_pay.List',compact('reasons'));
        /**
        $reson =Reason_Pay::all();
        return DataTables::of($reson)->addColumn('action',function ($reson){
            return '<a href="#" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i> show</a>'
                .'<a onclick="editForm('.$reson->reason_pay_id.')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a>'.
                '<a onclick="deleteData('. $reson->reason_pay_id.')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i>Delete </a>';

        })->make(true);
         * */

    }



    public function store(Request $request)
    {

            $this->validate($request, [
                'title' => 'required'
            ], [
                'title.required' => 'وارد کردن نوع یا از بابت مصارف الزامی میباشد'
            ]);
           if($request->ajax()){
               $res =Reason_Pay::create($request->all());
               $r =$this->find($res->id);

               return response($r);
           }


    }
    public function find($id){
        return Reason_Pay::find($id);
    }

    public function edit($reason_pay_id)
    {
        $reason_pay=Reason_Pay::find($reason_pay_id);
        return view('management.reason_pay.edit',compact('reason_pay'));
    }

    public function update(Request $request,$id)
    {
        $reason_pay=Reason_Pay::where("expense_reason_id",$id)->first();

        if($request->isMethod('get'))

            return view('reason_pay.form',compact('reason_pay'));

        else {
            $validator = Validator::make(Input::all(), [
                "title" => "required"
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
            $reason= Reason_Pay::where("expense_reason_id",$id)->update([

                "title"=> $request->input("title")

            ]);
            return array(
                'content' => 'content',
                'url' => route('reason_pay.list')
            );
        }

    }

    public function delete($id)
    {

        if ($id && ctype_digit($id)) {
            $user = \DB::table('expense_reason')->where('expense_reason_id', $id)->update(['status' => 1]);
            return redirect()->route('reason_pay.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
        }
    }

    public function search($id)
    {
        $data = \DB::table('expense_reason')
            ->select("expense_reason_id", "title")
            ->where('expense_reason_id', 'LIKE', "%$id%")
            ->orWhere('title', 'LIKE', "%$id%")
            ->where("expense_reason.status", "!=", "1")
            ->get();

        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }

}
