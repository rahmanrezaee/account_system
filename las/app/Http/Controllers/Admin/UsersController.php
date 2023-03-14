<?php

namespace App\Http\Controllers\Admin;
use App\Models\Agency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;


class UsersController extends Controller
{
    public function index($id = null){

        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
        {
            if (isset($id) && $id>0){
                $users=User::paginate($id);
            }else{
                $users=User::paginate(5);
            }
        } elseif (Auth::user()->user_level == 2) {
            if (isset($id) && $id>0){
                $users = User::where('agency_id','=',Auth::user()->agency_id)->paginate($id);
            }else{
                $users = User::where('agency_id','=',Auth::user()->agency_id)->paginate(5);
            }
        } else {
            if (isset($id) && $id>0){
                $users = User::where('user_id','=',Auth::user()->user_id)->paginate($id);
            }else{
                $users = User::paginate(5);
            }
        }

        return view('user.index',compact('users'))->with(['panel_title'=>'لیست کاربرها', 'route' => route('user.list')]);

    }
    public function create(Request $request){

        if($request->isMethod('get')){
            $agencies = Agency::all();
            return view('user.form', compact('agencies'))->with(['panel_title'=>'ایجاد کاربر جدید']);
        }
        else{
            $validator =Validator::make(Input::all(),[
                'name'=>'required',
                'username'=>'required|email',
                'password'=>'required|confirmed',

            ]);
            if($validator->fails()){


                return array(
                    'fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray(),

                );

            }

            $user =new User();
            $user->name=Input::get('name');
            $user->last_name= Input::get('last_name')?Input::get('last_name'):'';
            $user->username=Input::get('username');
            $user->password=bcrypt(Input::get('password'));
            $user->user_level= Input::get('user_level');
            if (Input::get('user_level') == 1){
                $user->agency_id = 0;
            } else {
                if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
                {
                    $user->agency_id= Input::get('agency_id');
                } else {
                    $user->agency_id= Auth::user()->agency_id;
                }
            }

            $user->save();


        }
        return array(
            'content'=>'content',
            'url'=>route('user.list')
        );

    }

    public function editUserPublicInfo(Request $request,$id){

        $user=User::find($id);



        if($request->isMethod('get')) {
            $agencies = Agency::all();
            return view('user.editUserPublicInfo', compact('user', 'agencies'))->with(['panel_title' => 'ویرایش  کتابخانه ']);
        }else {

            $rules=[];
            if(strtolower($user->username)!=strtolower(Input::get('username')))
                $rules+=['username'=>'required|email|unique:users'];


            $validator =Validator::make(Input::all(),$rules);
            if($validator->fails()){
                return array('fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray());
            }

            $user->name = Input::get('name');
            $user->last_name = Input::get('last_name');
            $user->username = Input::get('username');
            $user->user_level = Input::get('user_level');

            if (Input::get('user_level') == 1){
                $user->agency_id = 0;
            } else {
                if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
                {
                    $user->agency_id= Input::get('agency_id');
                } else {
                    $user->agency_id= Auth::user()->agency_id;
                }
            }

            $user->save();

            return array(
                'content' => 'content',
                'url' => route('user.list')
            );


            //Session::put('msg_status', 'fkjdkfgjdlgjdlkgjdkgjdl');
        }

    }

    public function editUserSecurityInfo(Request $request,$id){

        if($request->isMethod('get')) {
            $status=1;

            return view('user.editUserSecurityInfo', ['user' => User::find($id)],compact('status'))->with(['panel_title' => 'ویرایش اطلاعات امنییتی کاربر']);

        } else{

            $user = User::find($id);

            $password=$user->password;
            $old_password=$request->get('old-password');
            $rules=[];

            if(Input::get('password')!='')

                $rules+=['password'=>'confirmed'];

            $validator =Validator::make(Input::all(),$rules);
            if($validator->fails()){
                return array('fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray());
            }

            if(Hash::check($old_password, $password)){

                $user->user_level=Input::get('user_level');
                if(Input::get('password') != '')
                    $user->password = bcrypt(Input::get('password'));
                $user->save();

            }

            return array(
                'content'=>'content',
                'url'=>route('user.list')
            );
        }
    }



    public function delete($id){

        $user=User::find($id);
        $user->status=1;
        $user->save();
        return redirect()->route('user.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');

    }

    public function doLogout(){
        Auth::logout();
        return redirect('/');
    }

    public function search($id)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
        {

            $data = \DB::table('user')
                ->select('user.user_id','user.name','user.last_name','user.username','user_level','status')
                ->where('user.user_id', 'LIKE', "%$id%")
                ->orWhere('user.name', 'LIKE', "%$id%")
                ->orWhere('user.last_name', 'LIKE', "%$id%")
                ->orWhere('user.username', 'LIKE', "%$id%")
                ->orWhere('user.user_level', 'LIKE', "%$id%")
                ->where('user.status','!=',1)
                ->get();
        } else {
            $data = \DB::table('user')
                ->select('user.user_id','user.name','user.last_name','user.username','user_level','agency_id','status')
                ->where('user.agency_id','=', Auth::user()->agency_id)
                ->where(function($q) use ($id) {
                    $q->where('user.user_id', 'LIKE', "%$id%")
                        ->orWhere('user.name', 'LIKE', "%$id%")
                        ->orWhere('user.last_name', 'LIKE', "%$id%")
                        ->orWhere('user.username', 'LIKE', "%$id%")
                        ->orWhere('user.user_level', 'LIKE', "%$id%");
                })
                ->where('user.status','!=',1)
                ->get();
        }



        if (count($data)>0){
            return response(array(
                'data'=>$data,
                'user_logged_in' => Auth::user()
            ));
        }
    }


    public function userInfo($id){
        $users = User::all()
            ->where('user_id','=',$id)
            ->where('user_id','=',\Auth::user()->user_id)
            ->where('status','=',0);
        return view('user.userInfo',compact('users'))->with(['panel_title'=>'مشخصات کاربر']);
    }

    public function editUserSecurity(Request $request,$id)
    {
        $user = User::find($id);
        if($request->isMethod('get')) {
            $status = 1;
            return view('user.simpleUserSecurityEdit',compact('status','user'))->with(['panel_title' => 'ویرایش اطلاعات امنییتی کاربر']);

        } else{
            $user =User::find($id);

            $password=$user->password;
            $old_password=$request->get('old-password');
            $rules=[];

            if(Input::get('password')!='')

                $rules+=['password'=>'confirmed'];

            $validator =Validator::make(Input::all(),$rules);
            if($validator->fails()){


                return array('fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray());
            }

            if(Hash::check($old_password, $password)){

                $user->user_level = \Auth::user()->user_level;
                if(Input::get('password')!='')
                    $user->password = bcrypt(Input::get('password'));

                $user->save();

            }

            return array(
                'content'=>'content',
                'url' => route('home.list'),
                "refresh" => true
            );

        }
    }

    public function editUserInfo(Request $request,$id)
    {
        $user = User::find($id);
        if($request->isMethod('get')) {
            return view('user.simpleUserInfoEdit', compact('user'))->with(['panel_title' => 'ویرایش  اطلاعات عمومی کاربر ']);
        }else {

            $rules=[];
            if(strtolower($user->username)!=strtolower(Input::get('username')))
                $rules+=['username'=>'required|alpha_dash|unique:users'];


            $validator =Validator::make(Input::all(),$rules);
            if($validator->fails()){


                return array('fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray());
            }

            $user->name = Input::get('name');
            $user->last_name = Input::get('last_name');
            $user->username = Input::get('username');
            $user->user_level = Input::get('user_level');


            $user->save();



            return array(
                'content' => 'content',
                'url' => route('home.list'),
                "refresh" => true
            );


            //Session::put('msg_status', 'fkjdkfgjdlgjdlkgjdkgjdl');
        }
    }

//    Active Or Inactive User Functioin
    public function changeStatus(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $user->status = $request->get('status');
        $user->update();

        return response()->json(['success'=>'Status change successfully.']);
    }

//    Change Branch
    public function changeBranches(Request $request)
    {
        $user = User::find(Auth::user()->user_id);
        $user->agency_id = $request->get('agency_id');
        $user->update();

        return response()->json(['success'=>'Agency change successfully.']);
    }
}