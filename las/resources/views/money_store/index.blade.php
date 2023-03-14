
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
            <th>اسم حساب</th>
            <th>مقدار موجودی</th>
            <th>واحد پولی</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">
        @if (isset($moneyStores)?$counter= 1:'')
            @foreach($moneyStores as $money)
                <tr>
                    <td>{{$counter++}}</td>
                    <td>{{ $money->name }}</td>
                    <td>{{ number_format($money->money_amount) }}</td>
                    <td>{{ $money->currency['currency_name'] }}</td>
                    <td id="operations">
                        <a href="javascript:ajaxLoad('{{route('money_exchange.update',$money->store_id)}}')"><span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span></a >
                        <a href="javascript:if(confirm('آیا میخواهید حساب تان را حذف کنید؟')) ajaxDelete('{{route('money_exchange.delete',$money->store_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $moneyStores->links() }}
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
                    url: 'money_store/search/'+search,
                    success: function (response) {
                        var trHTML = '';
                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.store_id + '</td><td>' +
                                item.name + '</td><td>'+
                                new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.money_amount) + '</td><td>'+
                                item.currency_name + '</td><td id="operations">'+
                                '<button  id-item=' + item.store_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_money_store" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.store_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_money_store" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('.loading').hide();
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_money_store", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("money_store/update/" + id);
            });

            // Delete money_store
            $("#content-display").on('click', ".delete_money_store", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("money_store/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>


