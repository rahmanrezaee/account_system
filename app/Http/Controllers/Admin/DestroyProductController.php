<?php

namespace App\Http\Controllers\Admin;

use App\Models\DestroyProduct;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class DestroyProductController extends Controller
{

    public function index(){

        $destories=DB::table('destroyed_product')->join('store','destroyed_product.store_id','=','store.store_id')->select('store.store_name','destroyed_product.store_id')->where('destroyed_product.status','=',0)->groupBy('store_name','store_id')->get();

        return view('destroy_product.index',compact('destories'));
    }
    public function detail($id){

        $detail=\DB::table('destroyed_product')->join('product','destroyed_product.product_id','=','product.product_id')->select('destroyed_product.package_price','destroyed_product.unit_price','destroyed_product.total_price','destroyed_product.quantity','destroyed_product.date','product.product_name')->where('destroyed_product.store_id','=',$id)->get();

        return view('destroy_product.index-detail',compact('detail'));
    }
    public  function edit(Request $request , $id){

        if($request->isMethod('get')){

            $store=\DB::table('destroyed_product')->join('store','destroyed_product.store_id','=','store.store_id')->select('destroyed_product.date','store.store_name')->where('destroyed_product.store_id','=',$id)->first();
           $destroy=\DB::table('destroyed_product')->join('product','destroyed_product.product_id','=','product.product_id')->join('product_store','product.product_id','=','product_store.product_id')->join('product_unit','product.unit_id','=','product_unit.unit_id')->select('destroyed_product.total_price','destroyed_product.quantity','destroyed_product.package_price','product.product_name','destroyed_product.product_id','destroyed_product.dest_pro_id','product_unit.unit_quantity','product_store.product_stor_id')->where('destroyed_product.store_id','=',$id)->get();
            return view('destroy_product.edit-form',compact('store','destroy','id'));
        }else{

            $validator =Validator::make(Input::all(),[
                'package_price'=>'required',
                'total_price'=>'required',
            ]);
            if($validator->fails()){

                return array(
                    'fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray(),
                );
            }
            if(count($request->product_name)>0){


                foreach ($request->product_name as $item=>$value){
                    $product_id=$request->product_id[$item];
                    $product_store=$request->product_store_id[$item];
                    $product_quantity=$request->product_quantity[$item];
                    $dest_pro_id=$request->dest_product_id[$item];
                    $quantity=DestroyProduct::find($dest_pro_id);
                    $qty=$request->product_unit_quantity[$item];
                    $pr=$request->package_price[$item];
                    $unit_price=$pr/$qty;
                    if($quantity->quantity==$request->product_quantity[$item]){

                    }else if($request->product_quantity[$item]>$quantity->quantity){
                        $form=$request->product_quantity[$item];
                        $dqty=$quantity->quantity;
                        $final=$form-$dqty;

                        DB::table('product_store')->where('product_stor_id','=',$product_store)->decrement('quantity',$final);

                    }else{
                        $dqty=$quantity->quantity;
                        $form=$request->product_quantity[$item];
                        $final=$dqty-$form;

                        DB::table('product_store')->where('product_stor_id','=',$product_store)->decrement('quantity',$final);

                    }
                    $data=[
                        'store_id'=>$id,
                        'unit_price'=>number_format($unit_price,2),
                        'product_id'=>$request->product_id[$item],
                        'package_price'=>$request->package_price[$item],
                        'total_price'=>$request->total_price[$item],
                        'quantity'=>$request->product_quantity[$item],
                        'user_id'=>Auth::user()->user_id,
                    ];
                    DB::table('destroyed_product')->where('dest_pro_id','=',$dest_pro_id)->where('store_id','=',$id)->update($data);
                }

                return array(

                    'content'=>'content',
                    'url'=>route('destroy_product.list'),
                );

            }

        }

    }
    public function create(Request $request){
        $stores=Store::all();
        if($request->isMethod('get')){

            return view('destroy_product.form',compact('stores'));
        }else{
            $validator =Validator::make(Input::all(),[
                'stack_name'=>'required',
                'product_name'=>'required',
                'product_id'=>'required',
                'pr_date'=>'required',
                'package_price'=>'required',
                'total_price'=>'required',
            ]);
            if($validator->fails()){

                return array(
                    'fail'=>true,
                    'errors'=>$validator->getMessageBag()->toArray(),
                );
            }
            if(count($request->product_name)>0){


                foreach ($request->product_name as $item=>$value){
                    $product_store=$request->product_store_id[$item];
                    $product_quantity=$request->product_quantity[$item];
                    DB::table('product_store')->where('product_stor_id','=',$product_store)->decrement('quantity',$product_quantity);
                    $prDestroy=new DestroyProduct();
                    $prDestroy->store_id=$request->get('stack_name');
                    $prDestroy->product_id=$request->product_id[$item];
                    $prDestroy->total_price=$request->total_price[$item];
                    $qty=$request->product_unit_quantity[$item];
                    $prDestroy->quantity=$request->product_quantity[$item];
                    $pr= $prDestroy->package_price=$request->package_price[$item];
                    $unit_price=$pr/$qty;
                    $prDestroy->unit_price=number_format($unit_price,2);
                    $prDestroy->user_id=Auth::user()->user_id;
                    $prDestroy->date=$request->get('pr_date');
                    $prDestroy->save();
                }

                return array(

                    'content'=>'content',
                    'url'=>route('destroy_product.list'),
                );

            }

        }
    }

   public function search(Request $request){
        $stack_id=$request->get('stack_id');
        $query=$request->get('query');
        $name=$request->get('name');
        $destroy=\DB::table('product')->join('product_store','product.product_id','=','product_store.product_id')->join('product_unit','product.unit_id','=','product_unit.unit_id')->select('product.product_name','product.product_id','product_store.quantity','product_unit.unit_quantity','product_store.product_stor_id')->where($query,'like','%'.$name.'%')->where('product_store.store_id','=',$stack_id)->get();

        return json_encode($destroy);

   }
   public function  delete($id){
       $dest_product =DB::table('destroyed_product')->select('dest_pro_id')->where('store_id',$id)->get()->toArray();
       $data=array();
       foreach ($dest_product as $key=>$value){
           $data[$key]=$value->dest_pro_id;
       }
       foreach ($data as $key=>$value){

           DB::table('destroyed_product')->where('dest_pro_id',$value)->update(['status'=>1]);
       }

       return redirect()->route('destroy_product')->with('success', 'عملیه حذف با موفقیتت انجام شد');
   }



}
