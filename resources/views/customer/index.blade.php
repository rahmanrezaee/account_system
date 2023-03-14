
@include('notifications.notifications');

<div class="col-md-12">
    <h3 style="text-align: center" id="title">
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
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >ای دی</th>
            <th >نام</th>
            <th >کدنمبر </th>

            <th >شماره مبایل</th>
            <th >آدرس</th>

            <th id="operations_title">عملیات</th>


        </tr>
        </thead>
        <tbody id="content-display">
        @foreach($customer as $cus)

            <tr>
                <td>{{$cus->customer_id}}</td>
                <td>{{$cus->name}}</td>
                <td>{{$cus->customer_code}}</td>

                <td>{{$cus->phone}}</td>
                <td>{{$cus->address}}</td>

                <td colspan="2" id="operations">
                    <a href="javascript:if(confirm('Are you want to delete this record?'))ajaxDelete('{{route('customer.delete',$cus->customer_id)}}','{{csrf_token()}}')"><i class=" glyphicon glyphicon-trash btn btn-danger btn-sm" ></i></a>
                    <a href="javascript:ajaxLoad('{{route('customer.update',$cus->customer_id)}}')"><i class="glyphicon glyphicon-edit btn btn-primary btn-sm"></i></a>
                </td>

            </tr>

        @endforeach
        </tbody>

    </table>
    <div class="pagination" style="float: left">
        {{ $customer->links() }}
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
                        url: 'customer/search/'+search,
                        success: function (response) {
                            var trHTML = '';

                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.customer_id + '</td><td>' +
                                    item.name + '</td><td>' + item.customer_type +'</td><td>' +
                                    item.phone +'</td><td>' +  item.address +
                                    '</td><td id="operations">' +
                                        '<button  id-item=' + item.customer_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_customer" ></button>' + '&nbsp;' +
                                        '<button  id-item=' + item.customer_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_customer" ></button>' +
                                    '</td>'
                                '</tr>';

                            });
                            $('.loading').hide();
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_customer", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("customer/update/" + id);
                });

                // Delete Customer
                $("#content-display").on('click', ".delete_customer", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("customer/delete/" + id ,$('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })


        })


    </script>

</div>

