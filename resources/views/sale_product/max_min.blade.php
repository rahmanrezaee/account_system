




<div class="col-md-12">

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
        @foreach($sale_products as $sale_product)
    
    
            <tr>
            <td>{{$sale_product->product_name}}</td>
            <td>{{$sale_product->quantity}}</td>
            <td>{{$sale_product->sale_factor_id}}</td>
            <td>{{$sale_product->sale_date}}</td>
             </tr>

        @endforeach

        </tbody>
        <tfoot>
        <tr>
            
            <th> </th>
            <th> </th>
            <th></th>
            <th style="text-align: left">
                <a href="javascript:ajaxLoad('sale_product')" class="btn btn-danger">Back</a>
                <a href="{{route('sale_product.pdf')}}" id="create_pdf"><i class=" btn fa fa-file-pdf-o" style="color: red; font-size: 22px;"></i></a>
    
                
            
            </th>
    
    
        </tr>
    
    
        </tfoot>
    </table>
    <script>

        $(document).ready(function () {

            $('#example').dataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'pdf', 'print'
                ],
            });
        });
        $(document).ready(function () {
    
            $('#create').on('click', function (e) {
                e.preventDefault();
                var data = $(this).serialize();
               // var url = $(this).attr('action');
        
                // var Post = $(this).attr('method');
                $.ajax({
                    type: 'GET',
                    url: "{{route('sale_product.pdf')}}",
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('#table_report').html(data.table_data)
                       
                
                
                    }
                });
            });
    
        });
    </script>

</div>
