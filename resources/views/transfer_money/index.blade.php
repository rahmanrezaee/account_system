<div class="col-md-12">
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
                <th >شماره</th>
                <th> حساب فرستنده</th>
                <th>حساب دریافت کننده</th>
                <th>مقدار پول</th>
                <th>نرخ</th>
                <th>تاریخ</th>
                <th>توضیحات</th>
                <th id="operations_title">عملیات</th>
            </tr>
        </thead>
        <tbody id="content-display">
        @if (isset($moneyTransfers)?$counter= 1:'')
            @foreach($moneyTransfers as $transfer)
            <tr>
                <td>{{$counter++}}</td>
                <td>@if ($transfer->sender_id == $transfer->storeMoneySender['store_id'])
                    {{ $transfer->storeMoneySender['name'] }}
                @endif</td>
                <td>@if ($transfer->receiver_id == $transfer->storeMoneyReceiver['store_id'])
                        {{ $transfer->storeMoneyReceiver['name'] }}
                    @endif</td>
                <td>{{number_format($transfer->payment_amount)}}</td>
                <td>{{$transfer->rate}}</td>
                <td>{{$transfer->date}}</td>
                <td>{{$transfer->description}}</td>
                <td id="operations">
                    <a href="javascript:ajaxLoad('{{route('money_transfer.update', $transfer->transfer_id)}}')"><span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span></a >
                    <a href="javascript:if(confirm('آیا مطمئن هستید می خواهید حذف کنید؟')) ajaxDelete('{{route('money_transfer.delete',$transfer->transfer_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                 </td>
            </tr>
            @endforeach
        @endif
        </tbody>
        <tfoot>
        <tr>
            <th >شماره</th>
            <th> حساب فرستنده</th>
            <th>حساب دریافت کننده</th>
            <th>مقدار پول</th>
            <th>نرخ</th>
            <th>تاریخ</th>
            <th>توضیحات</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </tfoot>
    </table>
    <div class="pagination" style="float: left">
        {{ $moneyTransfers->links() }}
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

            if (search != '') {
                $('.loading').show();
                $.ajax({
                    type: 'get',
                    url: 'money_exchange/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.transfer_id + '</td>';
                                if (item.store_id == item.sender_id)
                                    trHTML += '<td>'+item.name+'</td>';

                                if (item.receiver_id == item.store_id)
                                    trHTML += '<td>'+item.name+'</td>';
                                else
                                    trHTML += '<td>'+item.receiver_id+'</td>';

                                trHTML += '<td>'+ new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.payment_amount) + '</td><td>'+
                                item.rate + '</td><td>'+
                                item.date + '</td><td>'+
                                item.description + '</td><td id="operations">'+
                                '<button  id-item=' + item.transfer_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_money_transfer" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.transfer_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_money_transfer" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('.loading').hide();
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_money_transfer", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("money_exchange/update/" + id);
            });

            // Delete money_transfer
            $("#content-display").on('click', ".delete_money_transfer", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("money_exchange/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>