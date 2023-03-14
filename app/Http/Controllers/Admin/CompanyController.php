<?php

namespace App\Http\Controllers\Admin;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Spatie\DbDumper\Databases\MySql;

class CompanyController extends Controller
{
    public function index($id = null)
    {

        if (isset($id) && $id > 0) {


            $companies = Company::where('status', '<', 1)->paginate($id);

        } else {

            $companies = Company::where('status', '<', 1)->paginate(3);

        }
        return view('company.index', compact('companies'))->with(['panel_title' => 'لیست شرکت ها', "route" => route('company.list')]);


    }

    public function create(Request $request)
    {

        if ($request->isMethod('get'))

            return view('company.form')->with(['panel_title' => 'ایجاد شرکت جدید']);
        else {
            $validator = Validator::make(Input::all(), [
                "company-name" => "required",
                "phone" => "required",
                "address" => "required",
            ]);
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
            $company = new Company();
            $company->company_name = Input::get('company-name');
            $company->phone = Input::get('phone');
            $company->address = Input::get('address');
            $company->status = 0;
            $company->save();
            Session::put('msg_status', true);
            return array(

                'content' => 'content',
                'url' => route('company.create')
            );

        }


    }

    public function update(Request $request, $id)
    {

        $company = Company::find($id);
        if ($request->isMethod('get'))

            return view('company.form', compact('company'))->with(['panel_title' => 'ویرایش شرکت جدید']);
        else {

            $validator = Validator::make(Input::all(), [
                'company-name' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ]);
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }

            $company->company_name = Input::get('company-name');
            $company->phone = Input::get('phone');
            $company->address = Input::get('address');
            $company->save();
            Session::put('msg_status', true);
            return array(

                'content' => 'content',
                'url' => route('company.list')
            );


        }
    }

    public function delete($id)
    {
        $company = Company::find($id);
        $company->status = 1;
        $company->save();
        return redirect()->route('company.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');

    }

    public function search($id)
    {
        $data = \DB::table('company')
            ->select('company.company_id', 'company.company_name', 'company.phone', 'company.address')
            ->where('company.company_id', 'LIKE', "%$id%")
            ->orWhere('company.company_name', 'LIKE', "%$id%")
            ->orWhere('company.phone', 'LIKE', "%$id%")
            ->orWhere('company.address', 'LIKE', "%$id%")
            ->where('company.status', '!=', 1)
            ->get();


        if (count($data) > 0) {
            return response(array(
                'data' => $data
            ));
        }
    }

}
