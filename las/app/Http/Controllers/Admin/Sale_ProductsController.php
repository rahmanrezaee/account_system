<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Reason_Pay;
use App\Models\Sale_Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use Barryvdh\DomPDF\PDF;
use function Symfony\Component\VarDumper\Dumper\esc;

class Sale_ProductsController extends Controller
{
    //
    public function index()
    {
        $sale_products=Sale_Product::all();
        return view('sale_product.index',compact('sale_products'));
    }

    public function report()
    {
        $products =Product::select('product_id','product_name')->get();
        $stores=Store::all();
        return view('sale_product.report', compact('products','stores'));

    }

    public function find_min_max()
    {
        $sale_products=DB::table('sale_product')
            ->join('product','product.product_id','=','sale_product.product_id')
            // ->join('store','store.store_id','=','sale_product.store_id ')
            ->select('product.product_name','sale_product.quantity','sale_product.sale_factor_id','sale_product.sale_date')
            ->orderBy('sale_product.quantity','DESC')
            ->get();
        return $sale_products;

    }

    public function max_min()
    {
        $sale_products= $this->find_min_max();
        return view('sale_product.max_min',compact('sale_products'));
    }

    public function report_data(Request $request)
    {

        if ($request->ajax()) {

            $output = '';
            $product_id=$request->get('product');
            $store_id=$request->get('store');
            if(ctype_digit($product_id)){
                if(ctype_digit($store_id)){
                    $start_date=$request->get('start_date');
                    $end_date=$request->get('end_date');


                   $data= DB::table('product')
                       ->join('product_store','product.product_id','=','product_store.product_id')
                       ->join('store','store.store_id','=','product_store.store_id')
                        //->join('product_store', 'product.product_id', '=', 'product_store.product_id')
                      // ->join('store','product_store.product_id','=','product_store.store_id')
                        ->select('product_name','store.store_name','product_store.quantity','product.procuct_buy_price','product.product_sale_price','product.product_created_at')
                       ->where('product.product_id',$product_id)
                       ->where('product_store.store_id',$store_id)
                       ->whereBetween('product_created_at',[$start_date,$end_date])
                        ->get();
                   //return response($data);

                }
            }



            $total_row = $data->count();
            if ($total_row > 0) {
                foreach ($data as $row) {
                    $output .= '
        <tr>
         <td>' . $row->product_name . '</td>
         <td>' . $row->store_name . '</td>
         <td>' . $row->quantity. '</td>
         <td>' . $row->procuct_buy_price . '</td>
         <td>' . $row->product_sale_price . '</td>
         <td>' . $row->product_created_at . '</td>
         
        </tr>
         ';
                }


            } else {
                $output = '
       <tr>
        <td align="center" colspan="5">No Data Found</td>
       </tr>
       ';
            }
            $data = array(
                'table_data' => $output,
                'total_data' => $total_row
            );

            echo json_encode($data);


        }


    }

    public function pdf()
    {
       $pdf=\App::make('dompdf.wrapper');
       $pdf->loadHTML($this->convert_data_to_html());
       return $pdf->stream();


    }

    public function convert_data_to_html()
    {
        $sale_products=$this->find_min_max();
        $output='
<html>
<head>
<meta charset="UTF-8">
</head>
        <h2 style="text-align: center"> بیشترین و کمترین محصولات فروشده</h2>

    <table id="example" class="table table-striped table-bordered display" style="width:100%">
        <thead>
        <tr>
            
            <th> نام محصول</th>
            <th> تعداد</th>
            <th> شماره فکتور</th>
            
            <th>تاریخ فروش</th>
         

        </tr>
        </thead>
        <tbody>
        ';
        foreach ($sale_products as $sale_product){
            $output .='
            <tr>
            <td>'.$sale_product->product_name.'</td>
            <td>'.$sale_product->quantity.'</td>
            <td>'.$sale_product->sale_factor_id.'</td>
            <td>'.$sale_product->sale_date.'</td>
             </tr>
            
            ';
        }
        $output .='
        </tbody>
        <tfoot>
        
</tfoot>
</table>
</html>
        ';
        return $output;

    }
}
