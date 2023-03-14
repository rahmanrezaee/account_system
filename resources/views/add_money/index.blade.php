<div class="col-md-12">
    <h4 style="text-align: center">
        {{isset($panel_title) ? $panel_title :''}}
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
            <th>نوعیت حساب</th>
            <th> مقدار پول</th>
            <th>تاریخ</th>
            <th>توضیحات</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">

        @if (isset($addMoney)?$counter= 1:'')
            @foreach($addMoney as $money)
                <tr>
                    <td>{{$counter++}}</td>
                    <td>{{$money->moneyStore['name']}}</td>
                    <td>{{ number_format($money->money_amount) }}</td>
                    <td>{{$money->date}}</td>
                    <td>{{$money->description}}</td>
                    <td id="operations">
                        <a href="javascript:ajaxLoad('{{route('add_money.update',$money->add_money_id)}}')"><span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span></a >
                        <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('add_money.delete',$money->add_money_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $addMoney->links() }}
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
                    url: 'add_money/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.add_money_id + '</td><td>' +
                                item.name + '</td><td>'+
                                item.money_amount+ '</td><td>'+
                                item.date + '</td><td>'+
                                item.description + '</td><td id="operations">'+
                                '<button  id-item=' + item.id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_add_money" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_add_money" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('.loading').hide();
                        $('#content-display').html(trHTML)
                    }
                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_add_money", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("add_money/update/" + id);
            });

            // Delete add_money
            $("#content-display").on('click', ".delete_add_money", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("add_money/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>

