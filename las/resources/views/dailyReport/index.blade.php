<div class="col-md-12">
    <h3 style="text-align: center">لیست فکتور های خارج شده ازگدام</h3>
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
        <div class="col-md-3"></div>
        <div class="col-md-6 d-flex" style="justify-content: flex-end">

            <div class="input-group">

                <select class="form-control" id="search-reason">
                    <option value="byCodeNumber">جستجو با کد نمبر فاکتور</option>
                    <option value="byPhone">جستجو با شماره تلفون مشتری</option>
                </select>
            </div>


            <div class="input-group">
                <input type="text" class="form-control" id="search" placeholder="Search" autocomplete="off">
                <div class="input-group-addon">
                    <i class="glyphicon glyphicon-search" id="search_icon"></i>
                </div>
            </div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered display" style="width:100%">
        <thead>
        <tr>
            <th>ادی</th>
            <th>کود</th>
            <th>گدام</th>
            <th>مقدار پرداخت (افغانی)</th>
            <th>مقدار باقیات (افغانی)</th>
            <th>مجموع</th>
            <th>تاریخ</th>
            <th>مشتری</th>
            <th id="operations_title"> عملیات</th>

        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ادی</th>
            <th>کود</th>
            <th>گدام</th>
            <th>مقدار پرداخت</th>
            <th>مقدار باقیات</th>
            <th>مجموع</th>
            <th>تاریخ</th>
            <th>مشتری</th>
            <th id="operations_title"> عملیات</th>
        </tr>
        </tfoot>
        <tbody id="content-display">
        @foreach($sale_factors as $sale_factor)

            <tr>
                <td>{{$sale_factor->sale_factor_id}}</td>
                <td>{{$sale_factor->sale_factor_code}}</td>
                <td>{{$sale_factor->store_name}}</td>
                <td>{{ number_format($sale_factor->recieption_price) }}</td>
                <td>{{ number_format($sale_factor->total_price - $sale_factor->recieption_price) }}</td>
                <td>{{ number_format($sale_factor->total_price) }}</td>
                <td>{{$sale_factor->sale_date}}</td>
                <td id="customer_id">{{$sale_factor->name}}</td>
                <td style="text-align: center" id="operations">
                    <a class="btn btn-info btn-xs " target="_blank" href="{{ route('sale_factor.print',$sale_factor->sale_factor_id) }}" >پرنت <i class="fa fa-print"></i></a>
                    <a href="javascript:ajaxLoad('sale_factor/update/{{ $sale_factor->sale_factor_id }}')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="javascript:ajaxLoad('sale_factor/returnFactor/{{ $sale_factor->sale_factor_id }}')" class="btn btn-success btn-xs">بازگشت اجناس</a>
                    <a href="javascript:ajaxLoad('{{ route('sale_factors.customerPaymentList',$sale_factor->sale_factor_id) }}')" class="btn btn-success btn-xs">لیست پرداختی</a>
                    <button sale_factor_id="{{ $sale_factor->sale_factor_id }}" class="btn btn-primary btn-xs showModel" ><i class="glyphicon glyphicon-eye-open"></i></button>
                    <a href="javascript:if(confirm('Are you sure you want to delete this?')) ajaxDelete('{{route('sale_factor.delete',$sale_factor->sale_factor_id)}}','{{csrf_token()}}')"
                       class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i>
                    </a>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $sale_factors->links() }}
    </div>

    <div id="myModal" class="modal" style="top: 100px; " tabindex="-1"  role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">اجناس شامل فکتور</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <table class="table" id="factor">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">نام محصول</th>
                            <th scope="col">تعداد</th>
                            <th scope="col">قیمت</th>
                            <th scope="col">مجموعه قیمت</th>
                        </tr>
                        </thead>
                        <tbody class="modal-content-in">

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
                var search_reason = $('#search-reason').val();

                if (search != '') {
                    $('.loading').show();
                    $.ajax({
                        type: 'get',
                        url: '{{ route('sale_factors.filterFactors') }}',
                        data:{
                            'search-reason': search_reason,
                            'keyword' : search,
                        },
                        success: function (response) {
                            var trHTML = '';

                            $.each(response.data, function (i, item) {
                                trHTML += '<tr>' +
                                    '<td>' + i + '</td>' +
                                    '<td>' + item.sale_factor_code + '</td>' +
                                    '<td>'+ item.store_name + '</td>' +
                                    '<td>'+ item.recieption_price + '</td>' +
                                    '<td>'+ (item.total_price - item.recieption_price )+ '</td>' +
                                    '<td>'+ item.total_price + '</td>' +
                                    '<td>'+ item.sale_date + '</td>' +
                                    '<td>'+ item.name + '</td>' +
                                    '<td style="text-align: center" id="operations">' +
                                    '                    <a class="btn btn-info btn-xs" target="_blank" href="sale_factor/print/'+item.sale_factor_id+'">' +
                                    'پرنت' +
                                    '</a>'+
                                    '                    <a href="javascript:ajaxLoad(\'sale_factor/update/'+item.sale_factor_id+'\')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></a>' +
                                    '                    <a href="javascript:ajaxLoad(\'sale_factor/returnFactor/'+item.sale_factor_id+'\')" class="btn btn-success btn-xs">بازگشت اجناس</a>' +
                                    '                    <a href="javascript:ajaxLoad(\'sale_factor/customerPaymentList/'+item.sale_factor_id+'\')" class="btn btn-success btn-xs">لیست پرداختی</a>' +
                                    '                    <button sale_factor_id="'+item.sale_factor_id+'" class="btn btn-primary btn-xs showModel" ><i class="glyphicon glyphicon-eye-open"></i></button>' +
                                    '                   <button onclick="deleteRoute('+item.sale_factor_id+')" class="btn btn-danger btn-xs"> <i class="glyphicon glyphicon-trash"></i></a>'+
                                    '</td>'
                                '</tr>';

                            });
                            $('.loading').hide();
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_sale_factor", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("sale_factor/update/" + id);
                });

                // Delete sale_factor
                $("#content-display").on('click', ".delete_sale_factor", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("sale_factor/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })
            $(document).on('click',".showModel",function () {

                var d = $(this).attr("sale_factor_id");

                $.ajax({
                    url:"sale_factor/details/"+d,
                    method:"GET",
                    success:function (data) {

                        console.log(data)
                        var html = "";
                        if (data.length > 0 ){

                            data.forEach( (index) => {

                                html+="<tr>" +
                                    "<td>"+index.product_name+"</td>" +
                                    "<td>"+index.quantity+"</td>" +
                                    "<td>"+index.buy_price+"</td>" +
                                    "<td>"+(index.sale_price * index.quantity)+"</td>" +

                                    "</tr>";

                            })
                            $(".modal-content-in").html(html);

                            $("#myModal").modal("show")


                        } else {

                            $(".modal-content-in").html("<tr><td colspan='7' class='text-center'>هیج محصول موحود نیست</td></tr>")
                            $("#myModal").modal("show")
                        }



                    },error:function () {

                    }

                })

            })

            $('.print_factor').click(function () {

                var d = $(this).attr("sale_factor_print_id");

                $.ajax({
                    url:"sale_factor/details/"+ d,
                    method:"GET",
                    success:function (data) {

                        console.log(data)
                        var html = "";
                        if (data.length > 0 ){

                            data.forEach( (index) => {

                                html+="<tr>" +
                                    "<td>"+index.product_name+"</td>" +
                                    "<td>"+index.quantity+"</td>" +
                                    "<td>"+index.buy_price+"</td>" +
                                    "<td>"+(index.sale_price * index.quantity)+"</td>" +

                                    "</tr>";

                            })
                            $(".modal-content-in").html(html);

                            // $("#myModal").modal("show")
                            // $("#myModal").modal("hide")

                        } else {

                            $(".modal-content-in").html("<tr><td colspan='7' class='text-center'>هیج محصول موحود نیست</td></tr>")
                            // $("#myModal").modal("show")
                            // $("#myModal").modal("hide")
                        }

                        var customer = $("#customer_id").text();

                        var table = document.getElementById('factor');

                        var d = "<html><head>" +
                            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
                            "<style> th{text-align:right !important} th:last-child,td:last-child{display: none;} body{font-family:sahle}</style>"+
                            "</head><body style='direction: rtl;font-family:sahel'>"+ "<h1 class='text-center'>فاکتور فروش  : "+customer+" </h1>"+ table.outerHTML + "</body></html>";


                        newWin = window.open();
                        newWin.document.write(d);
                        newWin.setTimeout(function () {

                            newWin.print();
                            newWin.close();
                        },3000)

                    },error:function () {

                    }

                })
            })


        });
    </script>

</div>

<script type="text/javascript">


    function deleteRoute(id) {

        if(confirm('Are you sure you want to delete this?')){

            ajaxDelete('sale_factor/delete/'+id,'{{ csrf_token() }}')

        }

    }


    {{--function imprimir() {--}}

    {{--var d = $(this).attr("sale_factor_id");--}}

    {{--$.ajax({--}}
    {{--url:"sale_factor/details/"+d,--}}
    {{--method:"GET",--}}
    {{--success:function (data) {--}}

    {{--console.log(data)--}}
    {{--var html = "";--}}

    {{--data.forEach( (index) => {--}}

    {{--html+="<tr>" +--}}
    {{--"<td>"+index.product_name+"</td>" +--}}
    {{--"<td>"+index.quantity+"</td>" +--}}
    {{--"<td>"+index.buy_price+"</td>" +--}}
    {{--"<td>"+(index.sale_price * index.quantity)+"</td>" +--}}

    {{--"</tr>";--}}

    {{--})--}}
    {{--$(".modal-content-in").html(html);--}}

    {{--var customer = $("#customer_id").text();--}}

    {{--var table = $(".modal-content-in");--}}
    {{--var d = "<html><head>" +--}}
    {{--"<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +--}}
    {{--"<style> th{text-align:right !important} th:last-child,td:last-child{display: none;} body{font-family:sahle}</style>"+--}}
    {{--"</head><body style='direction: rtl;font-family:sahel'>"+ "<h1 class='text-center'>فاکتور فروش  : "+customer+" </h1>"+ table.outerHTML + "</body></html>";--}}


    {{--newWin = window.open();--}}
    {{--newWin.document.write(d);--}}
    {{--newWin.setTimeout(function () {--}}

    {{--newWin.print();--}}
    {{--newWin.close();--}}
    {{--},3000)--}}


    {{--},error:function () {--}}

    {{--}--}}

    {{--})--}}


    {{--}--}}
</script>






