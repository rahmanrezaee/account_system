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
            <th>مقدار درآمد (افغانی)</th>
            <th>تاریخ</th>
            <th>توضیحات</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">
            @foreach($cars as $car)
            <tr>
                <td>{{$car->car_revenue_id}}</td>
                <td>{{ number_format($car->revenue_amount) }}</td>
                <td>{{$car->date}}</td>
                <td>{{$car->description}}</td>
                <td id="operations">
                    <a href="javascript:ajaxLoad('{{route('car.update',$car->car_revenue_id)}}')"><span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span></a >
                    <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('car.delete',$car->car_revenue_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $cars->links() }}
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

        // search section
        $('#search_icon').click(function () {
            var search = $('#search').val();

            if (search != '') {
                $.ajax({
                    type: 'get',
                    url: 'car/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.car_revenue_id + '</td><td>' +
                                new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.revenue_amount) + '</td><td>'+
                                item.date + '</td><td>'+
                                item.description + '</td><td id="operations">'+
                                '<button  id-item=' + item.car_revenue_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_car" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.car_revenue_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_car" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_car", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("car/update/" + id);
            });

            // Delete car
            $("#content-display").on('click', ".delete_car", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("car/delete/" + id , $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>

</div>

