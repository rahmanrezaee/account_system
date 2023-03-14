<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Owner;

class OwnerController extends Controller
{
    public function index($id = null){

        if (isset($id) && $id>0){
            $owners = Owner::where('status', '!=', 1)->where('percentage','>',0)->paginate($id);
        }else{
            $owners = Owner::where('status', '!=', 1)->where('percentage','>',0)->paginate(5);
        }

        return view('owner.list_owner',compact('owners'))->with(['panel_title'=>'لیست شرکا', 'route' => route('owner.list')]);
      
    }

    // create owner
    public function create(Request $request){

        if ($request->isMethod('get'))

        return view('owner.create_owner')->with('panel_title','ثبت شریک جدید  ');

    else {
        $validator = Validator::make(Input::all(), [
            'full_name' => 'required',
            'percentage' => 'required',
            'date_share' => 'required',
        ]);
        if ($validator->fails()) {
            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray()
            );
        } else {

            $owner = new Owner();
            $owner->full_name = Input::get('full_name');
            $owner->percentage = Input::get('percentage');
            $owner->date_share = Input::get('date_share');
          
            $owner->save();
            return array(
                'content' => 'content',
                'url' => route('owner.list')
            );

        }
    }
}

   //   delete owner
   public function delete($id)
   {
       $owner=Owner::find($id);
       $owner->status=1;
       $owner->save();
       return redirect()->route('owner.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');
       
   }

//    update owner
   public function update(Request $request, $id)
   {

       $owner=Owner::find($id);
       if($request->isMethod('get'))

           return view('owner.create_owner',compact('owner'))->with(['panel_title'=>'ویرایش شرکا ']);
       else {
           $validator = Validator::make(Input::all(), [
            'full_name' => 'required',
            'percentage' => 'required',
            'date_share' => 'required',

           ]
           );
           if ($validator->fails()) {
               return array(
                   'fail' => true,
                   'errors' => $validator->getMessageBag()->toArray()
               );
           }

           $owner->full_name = Input::get('full_name');
           $owner->percentage = Input::get('percentage');
           $owner->date_share = Input::get('date_share');
        
           $owner->update();
           return array(
               'content' => 'content',
               'url' => route('owner.list')
           );

           Session::put('msg_status', 'fkjdkfgjdlgjdlkgjdkgjdl');
       }
   }

    public function search($id)
    {
        $data = \DB::table('owner')
            ->select('owner.owner_id','owner.full_name','owner.percentage','owner.date_share')
            ->where('owner.owner_id', 'LIKE', "%$id%")
            ->orWhere('owner.full_name', 'LIKE', "%$id%")
            ->orWhere('owner.percentage', 'LIKE', "%$id%")
            ->orWhere('owner.date_share', 'LIKE', "%$id%")
            ->where('owner.status','!=',1)
            ->get();


        if (count($data)>0){
            return response(array(
                'data'=>$data
            ));
        }
    }
}
