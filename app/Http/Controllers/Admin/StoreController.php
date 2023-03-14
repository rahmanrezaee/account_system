<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\MoneyStore;
use App\Models\Product_Store;
use App\Models\Store;
use App\Models\StoreProductsTran;
use Composer\Util\AuthHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use function Sodium\compare;
use Symfony\Component\VarDumper\Cloner\Data;

class StoreController extends Controller
{

    public function index($id = null)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0){
            if (isset($id) && $id>0){
                $stores = DB::table("store")
                    ->join("money_store","money_store.store_id", "=", "store.money_store_id")
                    ->join('agency', 'agency.agency_id','=','store.agency_id')
                    ->select("store.store_id","store.store_name","money_store.name","store.store_address", 'agency.agency_name')
                    ->where('store.status', '=', 0)
                    ->paginate($id);
            }else{
                $stores = DB::table("store")
                    ->join("money_store","money_store.store_id", "=", "store.money_store_id")
                    ->join('agency', 'agency.agency_id','=','store.agency_id')
                    ->select("store.store_id","store.store_name","money_store.name","store.store_address", 'agency.agency_name')
                    ->where('store.status', '=', 0)
                    ->paginate(5);
            }
        }else {
            if (isset($id) && $id>0){
                $stores = DB::table("store")
                    ->join("money_store","money_store.store_id", "=", "store.money_store_id")
                    ->join('agency', 'agency.agency_id','=','store.agency_id')
                    ->select("store.store_id","store.store_name","money_store.name","store.store_address", 'agency.agency_name')
                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                    ->where('store.status', '=', 0)
                    ->paginate($id);
            }else{
                $stores = DB::table("store")
                    ->join("money_store","money_store.store_id", "=", "store.money_store_id")
                    ->join('agency', 'agency.agency_id','=','store.agency_id')
                    ->select("store.store_id","store.store_name","money_store.name","store.store_address", 'agency.agency_name')
                    ->where('store.agency_id', '=', \Auth::user()->agency_id)
                    ->where('store.status', '=', 0)
                    ->paginate(5);
            }

        }
        return view('stock.index', compact('stores'))->with(['panel_title' => 'لیست گدام ها','route' => route('store.list')]);
    }

    public function stackReport()
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
        {
            $stores = Store::all()->where('status', '!=', 1);
        } else {
            $stores = Store::where('agency_id','=',\Auth::user()->agency_id)->where('status', '!=', 1)->get();
        }
        return view('stock.report_form', compact('stores'))->with('panel_title','گزارشات گدام ها');
    }

    public function searchStackReport(Request $request, $id = null)
    {
        $store_id = $request->get('stack_id');

        if (isset($id) && $id > 0)
        {

            $store = \DB::table('product_store')

                ->join('product', 'product_store.product_id', '=', 'product.product_id')
                ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
//                ->select(DB::raw('count(product.product_name) as name'),
//                    DB::raw('sum(product_store.quantity) as total'),'min_value', 'product.product_name', 'product.product_code', 'product_store.quantity', 'product_unit.unit_name')
//                ->groupBy('product.product_name',"min_value", 'product_store.quantity', 'product.product_code', 'product_unit.unit_name')

                ->select("product_id")
                ->selectRaw('SUM(`quantity`) `quantities`')
                ->groupBy('product_id')
                ->where('product_store.store_id', '=', $store_id)
                ->paginate($id);




//            $store = DB::table('product_store')
//                ->select("product_id")
//                ->selectRaw('SUM(`quantity`) `quantities`')
//                ->groupBy('product_id')
//                ->join('product', 'product_store.product_id', '=', 'product.product_id')
//                ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')
//
//                ->where('store_id', '=', $store_id)
//                ->paginate($id);

        } else {


            $store = \DB::table('product_store')

                ->join('product', 'product_store.product_id', '=', 'product.product_id')
                ->join('product_unit', 'product_unit.unit_id', '=', 'product.unit_id')

//                ->select(DB::raw('count(product.product_name) as name'),
//                    DB::raw('sum(product_store.quantity) as total'),'min_value', 'product.product_name', 'product.product_code', 'product_store.quantity', 'product_unit.unit_name')
//                ->groupBy('product.product_name',"min_value", 'product_store.quantity', 'product.product_code', 'product_unit.unit_name')

                ->select('product_store.product_id','product_name as name','product.min_value','product.product_code','product_unit.unit_name')
//                ->select('product_store.product_id','product_name as name','product.min_value','product.product_code','product.unit_name')
                ->selectRaw('SUM(quantity) quantities')
//                ->groupBy('product_store.product_id','product_name as name','product.min_value','product.product_code','product.unit_name')
                ->groupBy('product_store.product_id','product_name','product.min_value','product.product_code','product_unit.unit_name')
                ->where('product_store.store_id', '=', $store_id)  ->paginate(5);
        }

//        $name = 0;
//        $qty = 0;
//        foreach ($store as $key => $str) {
//            $name += $str->name;
//            $qty += $str->total;
//        }


        return \Response::JSON(array(
//            'name' => $name,
//            'total' => $qty,
            "data" => $store,
            "pagination" => (string) $store->links(),
        ));



    }

    public function create(Request $request)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
        {
            $moneyStore = MoneyStore::all()->where("status","0");
        } else {
            $moneyStore = MoneyStore::where('agency_id','=',\Auth::user()->agency_id)
                ->where("status","0")
                ->get();
        }
        if ($request->isMethod('get')) {
            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0){
                $agencies = Agency::all();
            }else{
                $agencies = Agency::where('agency_id','=',\Auth::user()->agency_id)->get();
            }
            return view('stock.form',compact("moneyStore",'agencies'))->with('panel_title', 'ثبت گدام جدید');
        } else {
            $validator = Validator::make(Input::all(), [

                'store_name' => 'required',
                'store_address' => 'required',
                "store_money" => "required",
                "agency_id" => "required",
            ]);
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray(),
                );
            }
            $store = new Store();
            $store->store_name = $request->get('store_name');
            $store->store_address = $request->get('store_address');
            $store->money_store_id = $request->get('store_money');

            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
            {
                $store->agency_id = $request->get('agency_id');
            } else {
                $store->agency_id = \Auth::user()->agency_id;
            }
            $store->save();
            return array(
                'content' => 'content',
                'url' => route('store.list'),
            );
        }

    }

    public function update(Request $request, $id)
    {

        $stock = Store::find($id);
        $moneyStore = MoneyStore::all()->where("status","0");

        if($request->isMethod("get")){
            if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0){
                $agencies = Agency::all()->where('status', '!=', 1);
            }else{
                $agencies = Agency::where('agency_id','=',\Auth::user()->agency_id)->where('status', '!=', 1)->get();
            }
            return view("stock.form",compact("stock","moneyStore",'agencies'))->with(['panel_title' => 'ویرایش گدام']);

        }else{

            $validator = Validator::make(Input::all(), [

                'store_name' => 'required',
                'store_address' => 'required',
                "store_money" => "required",
                "agency_id" => "required",
            ]);
            if ($validator->fails()) {

                return array(
                    'fail' => true,
                    'errors' => $validator->getMessageBag()->toArray(),
                );
            }
            $store = Store::find($id)->update([
                "store_name"=>$request->get('store_name'),
                "money_store_id"=>$request->get('store_money'),
                "store_address"=>$request->get('store_address'),
                "agency_id"=>$request->get('agency_id'),

            ]);
            return array(
                'content' => 'content',
                'url' => route('store.list'),
            );



        }

    }

    public function delete($id)
    {

        $store = DB::table("store")->where("store_id",$id)->update(["status" => 1]);

        return redirect()->route('store.list')->with('success', 'عملیه حذف با موفقیتت انجام شد');

    }

    public function edit(Request $request)
    {

        $validator = Validator::make(Input::all(), [
            'store_name' => 'required',
            'store_address' => 'required',

        ]);
        if ($validator->fails()) {

            return array(
                'fail' => true,
                'errors' => $validator->getMessageBag()->toArray(),
            );

        } else {
            $store_id = $request->get('store_id');

            $store = Store::find($store_id);
            $store->store_name = $request->get('store_name');
            $store->store_address = $request->get('store_address');
            $store->save();

            return array(
                'content' => 'content',
                'url' => route('store.list')
            );

        }


    }

    public function transStaff(Request $request)
    {

        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
        {
            $stores = Store::all()
                ->where('status', '=', 0);
        } else {
            $stores = Store::where('agency_id','=',\Auth::user()->agency_id)
                ->where('status', '=', 0)
                ->get();
        }

        if ($request->isMethod("get")) {

            return view("sale_factor.transStaff", compact("stores"));

        } else {

            if ($request->input('stack_name_sender') == $request->input('stack_name_receiver'))
            {
                return array(
                    'fail' => true,
                    'errors' => ['گدام ها باهم مشابه می باشد لطفا گدام های متفاوت انتخاب کنید']
                );
            } else {

                $productList = $request->input("product_id");
                $product_quantity = $request->input("product_quantity");

                for ($i = 0; $i < count($productList); $i++) {


                    $p = Product_Store::where("store_id", "=", $request->input("stack_name_receiver"))
                        ->where("product_id", "=", $productList[$i])->first();

                    if ($p) {

                        $p->quantity = $p->quantity + $product_quantity[$i];
                        $p->save();

                        $sender = Product_Store::where("store_id", "=", $request->input("stack_name_sender"))
                            ->where("product_id", "=", $productList[$i])->first();
                        $sender->quantity = $sender->quantity - $product_quantity[$i];
                        $sender->save();


                        $report = new StoreProductsTran();
                        $report->sender_store_id = $request->input("stack_name_sender");
                        $report->receiver_store_id = $request->input("stack_name_receiver");
                        $report->product_id = $productList[$i];
                        $report->quantity = $product_quantity[$i];
                        $report->date =$request->input("pr_date");
                        $report->save();



                    } else {

                        $newTrans = new Product_Store();
                        $newTrans->store_id = $request->input("stack_name_receiver");
                        $newTrans->product_id = $productList[$i];
                        $newTrans->quantity = $product_quantity[$i];
                        $newTrans->save();

                        $sender = Product_Store::where("store_id", "=", $request->input("stack_name_sender"))
                            ->where("product_id", "=", $productList[$i])->first();
                        $sender->quantity = $sender->quantity - $product_quantity[$i];
                        $sender->save();

                        $report = new StoreProductsTran();
                        $report->sender_store_id = $request->input("stack_name_sender");
                        $report->receiver_store_id = $request->input("stack_name_receiver");
                        $report->product_id = $productList[$i];
                        $report->quantity = $product_quantity[$i];
                        $report->date =$request->input("pr_date");
                        $report->save();

                    }


                }
            }

            return array(
                'content' => 'content',
                'url' => route("store.transStaff"),
            );

        }
    }

    public function search($id)
    {
        if (\Auth::user()->user_level == 1 && \Auth::user()->agency_id == 0)
        {
            $data = DB::table("store")
                ->join("money_store","money_store.store_id", "=", "store.money_store_id")
                ->select("store.store_id","store.store_name","money_store.name","store.store_address", 'agency.agency_name')
                ->where('money_store.name', 'LIKE', "%$id%")
                ->orWhere('store.store_id', 'LIKE', "%$id%")
                ->orWhere('store.store_name', 'LIKE', "%$id%")
                ->orWhere('store.store_address', 'LIKE', "%$id%")
                ->orWhere('store.store_address', 'LIKE', "%$id%")
                ->where('store.status', '!=', 1)
                ->get();
        } else {
            $data = DB::table("store")
                ->join("money_store","store.money_store_id","=","money_store.store_id")
                ->join('agency', 'store.agency_id','=','agency.agency_id')
                ->select('store.store_id',"store.store_name" ,'money_store.name','store.store_address','money_store.name',"agency.agency_name")
                ->where('store.agency_id','=',\Auth::user()->agency_id)
                ->where(function($q) use ($id) {
                    $q->where('money_store.name', 'LIKE', "%$id%")
                        ->orWhere('store.store_id', 'LIKE', "%$id%")
                        ->orWhere('store.store_name', 'LIKE', "%$id%")
                        ->orWhere('store.store_address', 'LIKE', "%$id%")
                        ->orWhere('store.store_address', 'LIKE', "%$id%");
                })
                ->where('store.status', '!=', 1)
                ->get();
        }


        if (count($data) > 0) {
            return response(array(
                'data' => $data,
                'user_logged_in' => \Auth::user()
            ));
        }

    }

}
