<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class AgencyController extends Controller
{
    public function index()
    {
        if (isset($id) && $id>0){
            $agencies = Agency::where('status','<', 1)->paginate($id);
        }else{
            $agencies = Agency::where('status','', 1)->paginate(5);
        }
        return view('agency.index',compact('agencies'))->with(['panel_title' => 'لیست نماینده گی ها' ,'route' => route('agency.list')]);

    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))
        {
            return view('agency.form')->with('panel_title','ثبت نماینده گی جدید');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'agency_name' => 'required',
                'location' => 'required',
                'address' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                $agency = new Agency();
                $agency->agency_name = $request->get('agency_name');
                $agency->location = $request->get('location');
                $agency->address = $request->get('address');
                $agency->save();

                return array(
                    'content' => 'content',
                    'url' => route('agency.list')
                );

            }
        }
    }


    public function update(Request $request, $id)
    {
        $agency = Agency::find($id);
        if ($request->isMethod('get'))
        {
            return view('agency.form', compact('agency'))->with('panel_title','ویرایش کردن نماینده گی');
        }
        else {
            $validator = Validator::make(Input::all(), [
                'agency_name' => 'required',
                'location' => 'required',
                'address' => 'required',
            ]);

            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {
                $agency->agency_name = $request->get('agency_name');
                $agency->location = $request->get('location');
                $agency->address = $request->get('address');
                $agency->update();
                return array(
                    'content' => 'content',
                    'url' => route('agency.list')
                );

            }
        }
    }

    public function delete($id)
    {
        $agency = Agency::find($id);
        $agency->delete();
        return redirect()->route('agency.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
    }

    public function search($id)
    {
        $data = \DB::table('agency')
            ->select('agency_id','agency_name','location','address')
            ->where('agency_id', 'LIKE', "%$id%")
            ->orWhere('agency_name', 'LIKE', "%$id%")
            ->orWhere('location', 'LIKE', "%$id%")
            ->orWhere('address', 'LIKE', "%$id%")
            ->where('status','!=',1)
            ->get();


        if (count($data)>0){
            return response(array(
                'data'=>$data
            ));
        }
    }
}
