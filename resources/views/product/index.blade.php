<div class="col-lg-12 col-md-12 col-sm-12">
    @include('notifications.notifications')
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :''}}
    </h4>
    <div class="row margin-bottom-10">
        <div class="col-md-2">
            <div class="form-group">
                <select class="list-page form-control" >
                    <option>select count</option>
                    <option>5</option>
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="input-group">
                <button id="print_button"  class="fa fa-print form-control" ></button>
            </div>
        </div>
        <div class="col-md-6"></div>
        <div class="col-md-3">
            <div class="input-group">
                <input type="text" class="form-control" id="search" placeholder="Search" autocomplete="off">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-search" id="search_icon"></i>
                </div>
            </div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>ای دی</th>
            <th>نام محصول</th>
            <th>کود محصول</th>
            <th>کتگوری</th>
            <th>فیصدی مالیات</th>
            <th>واحد</th>
            <th>حداقل تعداد</th>
            <th>توضیحات</th>
            <th id="operations_title">عملیات</th>

        </tr>
        </thead>
        <tbody id="content-display">
        @foreach($products as $product)

            <tr>
                <td>{{$product->product_id}}</td>
                <td>{{$product->product_name}}</td>
                <td>{{$product->product_code}}</td>
                <td>{{$product->category_name}}</td>
                <td>{{$product->text_mount}}</td>
                <td>{{($product->unit_quantity == 1 ? '': $product->unit_quantity . ' - ' ).  $product->unit_name}}</td>
                <td>{{$product->min_value}}</td>
                <td>{{$product->product_description}}</td>
                <td colspan="2" id="operations">
                    <a href="javascript:if(confirm('Are you want to delete this record?'))ajaxDelete('{{route('product.delete',$product->product_id)}}','{{csrf_token()}}')"><i
                                class=" glyphicon glyphicon-trash "></i></a>
                    <a href="javascript:ajaxLoad('{{route('product.update',$product->product_id)}}')"><i
                                class="glyphicon glyphicon-edit "></i></a>
                </td>

            </tr>

        @endforeach
        </tbody>

    </table>
    <div class="pagination" style="float: left">
        {{ $products->links() }}
    </div>
    <script>

        $(document).ready(function () {

            $('#print_button').on('click',function(){
                window.print()
            })

            $(".list-page").change(function () {

                console.log();
                ajaxLoad('<?php echo $route; ?>'+"/"+$(this).val())

            })

            $(".pagination a").unbind().bind("click",function (event) {
                event.preventDefault();

                $('li').removeClass('active');
                $(this).parent('li').addClass('active');

                var myurl = $(this).attr('href');

                ajaxLoad(myurl);


            })

            // Search with Enter Key
            var input =$("#search");
            input.keyup(function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $("#search_icon").click();
                }
            });

            // search section
            $('#search_icon').click(function () {
                var search = $('#search').val();

                if (search != '') {
                    $('.loading').show();
                    $.ajax({
                        type: 'get',
                        url: 'product/search/'+search,
                        success: function (response) {
                            var trHTML = '';

                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.product_id + '</td><td>' +
                                    item.product_name + '</td><td>' + item.product_code+'</td><td>' +
                                    item.category_name +'</td><td>' +  item.unit_name +
                                    '</td><td>' +new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.product_buy_price )+
                                    '</td><td>' +new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.product_sale_price) +
                                    '</td><td>' +item.product_description +
                                    '</td><td id="operations">' +
                                    '<button  id-item=' + item.product_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_product" ></button>' + '&nbsp;' +
                                    '<button  id-item=' + item.product_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_product" ></button>' +
                                    '</td>'
                                '</tr>';

                            });
                            $('.loading').hide();
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_product", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("product/update/" + id);
                });

                // Delete product
                $("#content-display").on('click', ".delete_product", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("product/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })


        })


    </script>
</div>

