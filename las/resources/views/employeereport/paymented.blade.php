
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
            <th >ای دی</th>
            <th>نام</th>
            <th >معاش</th>
            <th >صندوق مربوطه</th>
            <th >مقدار پرداخت شده</th>
            <th >مقدار قرضه</th>
            <th >تاریخ پرداخت</th>
            <th >ماه ازسال</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th >ای دی</th>
            <th>نام</th>
            <th >معاش</th>
            <th >صندوق مربوطه</th>
            <th >مقدار پرداخت شده</th>
            <th >مقدار قرضه </th>
            <th >تاریخ پرداخت </th>
            <th >ماه ازسال</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </tfoot>
        <tbody id="content-display">

        @foreach($salary as $sal)

            <tr>
                <td>{{$sal->payment_id}}</td>
                <td>{{$sal->first_name}}&nbsp;{{$sal->last_name}}</td>
                <td>{{ number_format($sal->salary) }}</td>
                <td>{{$sal->account_name}}</td>
                <td>{{ number_format($sal->payment_amount) }}</td>
                <td>{{ number_format($sal->payment_borrow) }}</td>
                <td>{{$sal->payment_date}}</td>
                <td>{{$sal->payment_month}}</td>
                <td colspan="2" id="operations">
                    <a href="javascript:ajaxLoad('{{route('employeereport.paymented_update',$sal->payment_id)}}')"><i class="glyphicon glyphicon-edit btn btn-primary btn btn-sm"></i></a>
                    <a href="javascript:if(confirm('Are you want to delete this record?'))ajaxDelete('{{route('employeereport.delete',$sal->payment_id)}}','{{csrf_token()}}')"><i class="glyphicon glyphicon-trash btn btn-danger btn-sm" ></i></a>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $salary->links() }}
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

            // search section
            $('#search_icon').click(function () {
                var search = $('#search').val();

                if (search != '') {
                    $.ajax({
                        type: 'get',
                        url: 'employeereport/search/'+search,
                        success: function (response) {
                            var trHTML = '';
                            '', '', '', '', '', '', '',''
                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.payment_id + '</td><td>' +
                                    item.first_name +'&nbsp;'+item.last_name + '</td><td>'+
                                    new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.salary) + '</td><td>'+
                                    item.name + '</td><td>'+
                                    new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.payment_amount) + '</td><td>'+
                                    new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.payment_borrow)+ '</td><td>'+
                                    item.payment_date + '</td><td>'+
                                    item.payment_month + '</td><td>'+'</td>'
                                '</tr>';

                            });
                            $('#content-display').html(trHTML)
                        }

                    })
                }

            })
        })
    </script>

</div>






