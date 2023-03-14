
<div class="col-md-12">

    <h3 style="text-align: center">
        {{isset($panel_title) ?$panel_title :''}}
    </h3>
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
    <table id="example" class="table table-striped table-bordered display" style="width:100%">
        <thead>
        <tr>
            <th>ادیِ</th>
            <th>عنوان</th>

            <th>توضیحات</th>
            <th>  تاریخ پرداخت</th>
            <th> مقدار </th>
            <th> واحدپولی</th>
            <th>   نرخ پولی</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ادیِ</th>
            <th>عنوان</th>

            <th>توضیحات</th>
            <th> مقدار </th>
            <th>  تاریخ پرداخت</th>
            <th> واحدپولی</th>
            <th>   نرخ پولی</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </tfoot>
        <tbody id="content-display">
        @foreach($expenses as $expense)
            <tr>
                <td>{{$expense->expense_id}}</td>
                <td>{{$expense->title}}</td>
                <td>{{$expense->description}}</td>
                <td>{{$expense->pay_date}}</td>
                <td>{{ number_format($expense->amount) }}</td>
                <td>{{$expense->currency_name}}</td>
                <td>{{ $expense->currency_rate }}</td>
                <td id="operations">
                    <a href="javascript:ajaxLoad('{{route('expense.update',$expense->expense_id)}}')" class="glyphicon glyphicon-edit btn btn-primary btn-xs" id="edit_expense" style="margin-left: 3%"></a>
                    <a href="javascript:if(confirm('واقعا میخواهید حذف کنید؟؟'))ajaxDelete('{{route('expense.delete',$expense->expense_id)}}','{{csrf_token()}}')" class="glyphicon glyphicon-trash btn btn-danger btn-xs" id="delete_expense"></a>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $expenses->links() }}
    </div>

    <script type="text/javascript">
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
                        url: 'expense/search/'+search,
                        success: function (response) {
                            var trHTML = '';
                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.expense_id + '</td><td>' +
                                    item.title + '</td><td>'+
                                    item.description + '</td><td>'+
                                    new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.amount) + '</td><td>'+
                                    item.currency + '</td><td>'+
                                    item.pay_date + '</td><td>'+
                                    item.first_name + '&nbsp;'+ item.last_name+'</td><td id="operations">'+
                                    '<button  id-item=' + item.expense_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_expense" ></button>' + '&nbsp;' +
                                    '<button  id-item=' + item.expense_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_expense" ></button>' +
                                    '</td>'
                                '</tr>';

                            });
                            $('.loading').hide();
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_expense", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("expense/update/" + id);
                });

                // Delete expense
                $("#content-display").on('click', ".delete_expense", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("expense/delete/" + id , $('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })
        })
    </script>
</div>



