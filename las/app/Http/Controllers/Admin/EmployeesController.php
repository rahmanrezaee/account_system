<?php

namespace App\Http\Controllers\Admin;
use App\Models\Agency;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{
    public function index($id = null)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
        {
            if (isset($id) && $id>0){
                $employees = Employee::where('status', '!=', 1)->paginate($id);
            }else{
                $employees = Employee::where('status', '!=', 1)->paginate(5);
            }
        } else {
            if (isset($id) && $id>0){
                $employees = Employee::where('agency_id','=', Auth::user()->agency_id)->where('status', '!=', 1)->paginate($id);
            }else{
                $employees = Employee::where('agency_id','=', Auth::user()->agency_id)->where('status', '!=', 1)->paginate(5);
            }
        }
        return view("employee.index", compact("employees"))->with(['panel_title' =>'لیست همه کارمندان', 'route' => route('employee.list')]);

    }

    public function showDetail($id)
    {
        $employee = Employee::find($id);

        return view("employee.showDetail",compact("employee"))->with('panel_title','نمایش جزئیات کارمند');

    }

    public function show_agreement_paper($id)
    {
        $emp = Employee::find($id);

        $path = $emp->agreement_paper;
        return response()->download(public_path($path));
    }

    public function create(Request $request)
    {


        $currencies = Currency::all()->where("status", "0");

        if ($request->isMethod('get')) {

            $position = Position::all();

            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0){
                $agencies = Agency::all();
            } else{
                $agencies = Agency::where('agency_id','=', Auth::user()->agency_id)->get();
            }


            return view('employee.form',compact("position",'agencies','currencies'))->with('panel_title','راجسترکارمند جدید');

        } else {
            $validator = Validator::make(Input::all(), [

                'name' => 'required',
                'last_name' => 'required',
                'salary' => 'required',
                'phone' => 'required |min:10 |max:16',
                'come_date' => 'required ',
                'shift' => 'required',
                'currency_id' => 'required',
                'email' => [
                    'required',
                    'email',
                        function ($attribute, $value, $fail) {
                            if (Employee::where('email',$value)->count() > 0) {
                                $fail($attribute.' ایمیل قبلا موجود است');
                            }
                        },
                ],
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            } else {

                \DB::beginTransaction();
                try{
                    if ($request->file("photo")) {
                    $photo_address = "image/" . time() . "." . $request->file("photo")->getClientOriginalExtension();
                    $request->file("photo")->move(public_path("image"), $photo_address);

                } else {

                    $photo_address = "image/empty_profile.jpg";
                }
                    if ($request->file("agreepaper")  ) {
                        $agree_address = "image/per_" . time() . "." . $request->file("agreepaper")->getClientOriginalExtension();
                        $request->file("agreepaper")->move(public_path("image"), $agree_address);

                    } else {

                        $agree_address = "image/agreement.jpg";
                    }

                    $arr = [
                        "first_name" => $request->input("name"),
                        "last_name" => $request->input("last_name"),
                        "position" => $request->input("position"),
                        "email" => $request->input("email"),
//                    "salary_type" => $request->input("salary_type"),
                        "phone" => $request->input("phone"),
                        "salary" => $request->input("salary"),
                        "shift" => $request->input("shift"),
                        "hire_date" => $request->input("come_date"),
                        "gender" => $request->input("gender"),
                        "marital_status" => $request->input("status"),
                        "address" => $request->input("address"),
                        "currency_id" => $request->input("currency_id"),
//                    "photo" => $photo_address,
//                    "agreement_paper" => $agree_address,
                    ];

//                    dd($arr);

                    if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
                    {
                        $arr['agency_id'] = $request->input("agency_id");
                    } else {
                        $arr['agency_id'] = Auth::user()->agency_id;
                    }

                    Employee::create($arr);
                    \DB::commit();
                    return array(
                        'content' => 'content',
                        'url' => route('employee.create')
                    );

                }catch (\Exception $exception){

                    \DB::rollBack();
                    return array(
                        'fail' => true,
                        'errors' => ['خطا ' => 'ثبت انجام نشد دوباره امتحان کنن']
                    );
                }

            }
        }
    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            Employee::find($id)->update(['status' => 1]);
            return redirect()->route("employee.list")->with('success', 'عملیه حذف با موفقیتت انجام شد');
        }
    }

    public function update(Request $request, $id)
    {

        $currencies = Currency::all()->where("status", "0");
        $employee = Employee::find($id);
        $position = Position::all();
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0){
            $agencies = Agency::all();
        } else{
            $agencies = Agency::where('agency_id','=', Auth::user()->agency_id)->get();
        }
        if ($request->isMethod('get'))

            return view('employee.form', compact('employee', 'position','agencies','currencies'))->with('panel_title','ویرایش کارمند');

        else {
            $validator = Validator::make(Input::all(), [

                'name' => 'required',
                'last_name' => 'required',
                'salary' => 'required',
                'phone' => 'required |min:10 |max:16',
                'come_date' => 'required ',
                'shift' => 'required'
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
            if ($request->file("photo")) {
                $photo_address = "image/" . time() . "." . $request->file("photo")->getClientOriginalExtension();
                $request->file("photo")->move(public_path("image"), $photo_address);

            } else {

                $photo_address = "image/empty_profile.jpg";
            }
            if ($request->file("agreepaper")  ) {
                $agree_address = "image/per_" . time() . "." . $request->file("agreepaper")->getClientOriginalExtension();
                $request->file("agreepaper")->move(public_path("image"), $agree_address);

            } else {

                $agree_address = "image/agreement.jpg";
            }


            $arr = [
                "first_name" => $request->input("name"),
                "last_name" => $request->input("last_name"),
                "position" => $request->input("position"),
                "email" => $request->input("email"),
               // "salary_type" => $request->input("salary_type"),
                "phone" => $request->input("phone"),
                "salary" => $request->input("salary"),
                "shift" => $request->input("shift"),
                "hire_date" => $request->input("come_date"),
                "gender" => $request->input("gender"),
                "status" => $request->input("status"),
                "address" => $request->input("address"),
                "currency_id" => $request->input("currency_id"),
               // "photo" => $photo_address,
                // "agreement_paper" => $agree_address,
                "status" => 0,
            ];
            if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
            {
                $arr['agency_id'] = $request->input("agency_id");
            } else {
                $arr['agency_id'] = Auth::user()->agency_id;
            }

            Employee::find($id)->update($arr);
            return array(
                'content' => 'content',
                'url' => route('employee.list')
            );
        }

    }

    public function search($id)
    {
        if (Auth::user()->user_level == 1 && Auth::user()->agency_id == 0)
        {
            $data = \DB::table('employee')
                ->select("employee_id", "first_name","last_name","salary","phone","address")
                ->where('employee_id', 'LIKE', "%$id%")
                ->orWhere('first_name', 'LIKE', "%$id%")
                ->orWhere('last_name', 'LIKE', "%$id%")
                ->orWhere('salary', 'LIKE', "%$id%")
                ->orWhere('phone', 'LIKE', "%$id%")
                ->orWhere('address', 'LIKE', "%$id%")
                ->where("employee.status", "!=", "1")
                ->get();

        } else {
            $data = \DB::table('employee')
                ->select("employee_id", "first_name","last_name","salary","phone","address")
                ->where('employee.agency_id','=',Auth::user()->agency_id)
                ->where(function($q) use ($id) {
                    $q->where('employee_id', 'LIKE', "%$id%")
                        ->orWhere('first_name', 'LIKE', "%$id%")
                        ->orWhere('last_name', 'LIKE', "%$id%")
                        ->orWhere('salary', 'LIKE', "%$id%")
                        ->orWhere('phone', 'LIKE', "%$id%")
                        ->orWhere('address', 'LIKE', "%$id%");
                })
                ->where("employee.status", "!=", "1")
                ->get();
        }
        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }

    }
}
