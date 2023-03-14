<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Http\Controllers\Controller;


class CategorysController extends Controller
{
    public function index($id = null)
    {
        if (isset($id) && $id>0){


            $category = Category::where('status', '!=', 1)->paginate($id);

        }else{

            $category = Category::where('status', '!=', 1)->paginate(5);

        }
        return view('category.index', compact('category'))->with(['panel_title' => 'لیست کتگوری ها', 'route' =>route('category.list') ]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('get'))
            return view('category.form')->with(['panel_title' => 'ایجاد کتگوری جدید']);
        else {
            $validator = Validator::make(Input::all(), [
                'category_name' => 'required'
            ],
                [
                    'category_name.required' => 'وارد کردن نام کتگوری الزامی میباشد!'
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
            $Category = new Category();
            $name = $Category->category_name = Input::get('category_name');
            $recourd = \DB::table('category')->where('category_name', $name)->doesntExist();
            if ($recourd) {
                $Category->save();
                return array(
                    'content' => 'content',
                    'msg' => 'insert',
                    'url' => route('category.list'),
                );

            } else {
                return array(
                    'content' => 'content',
                    'url' => route('category.list')

                );

            }


        }

    }

    public function createManval(Request $request)
    {
            $validator = Validator::make(Input::all(), [
                'category_name' => 'required'
            ],
                [
                    'category_name.required' => 'وارد کردن نام کتگوری الزامی میباشد!'
                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }
            $Category = new Category();
            $name = $Category->category_name = Input::get('category_name');
            $recourd = \DB::table('category')->where('category_name', $name)->doesntExist();
            if ($recourd) {
                $Category->save();
               return \Response::json($Category);

            } else {
                return null;
            }



    }

    public function update(Request $request, $id)
    {

        $Category = Category::find($id);
        if ($request->isMethod('get'))

            return view('category.form', compact('Category'))->with(['panel_title' => 'ویرایش کتگوری']);
        else {
            $validator = Validator::make(Input::all(), [
                'category_name' => 'required'
            ], [
                    'category_name.required' => 'وارد کردن نام کتگوری الزامی میباشد!',

                ]
            );
            if ($validator->fails()) {
                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray()
                );
            }


            $Category->category_name = Input::get('category_name');
            $Category->save();

            return array(
                'content' => 'content',
                'msg' => 'insert',
                'url' => route('category.list'),
            );
        }


    }

    public function delete($id)
    {
        if ($id && ctype_digit($id)) {
            $category = Category::where('category_id', $id)->delete();
            if ($category)
                return redirect()->route("category.list")->with('success', 'عملیه حذف با موفقیت انجام شد');
        }
    }

    public function search($id)
    {
        $data = \DB::table('category')
            ->select('category.category_id','category.category_name')
            ->where('category.category_id', 'LIKE', "%$id%")
            ->orWhere('category.category_name', 'LIKE', "%$id%")
            ->where('category.status','!=',1)
            ->get();


        if (count($data)>0){
            return response(array(
                'data'=>$data
            ));
        }
    }

    public function getCategoryOptions(){

        return response()->json(Category::where('status', '!=', 1)->get()) ;
    }
}