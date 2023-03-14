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
            <th>نام وسیله</th>
            <th>مقدار پول (افغانی)</th>
            <th>تاریخ</th>
            <th>توضیحات</th>

            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">

         @if (isset($first_equipments) ? $counter = 1: '')
             @foreach($first_equipments as $first_equipment)
                 <tr>
                     <td>{{$counter++}}</td>
                     <td>{{$first_equipment->equipment_name}}</td>
                     <td>{{ number_format($first_equipment->money_amount) }}</td>
                     <td>{{$first_equipment->date}}</td>
                     <td>{{$first_equipment->description}}</td>

                     <td id="operations">
                         <a href="javascript:ajaxLoad('{{route('first_equipment_money.update',$first_equipment->first_money_eq_id)}}')"><span><i class="glyphicon glyphicon-edit btn btn-success btn-sm" title="ویرایش" ></i></span></a >
                         <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('first_equipment_money.delete',$first_equipment->first_money_eq_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash btn btn-danger btn-sm"></i></span></a>
                     </td>
                 </tr>
             @endforeach
         @endif

        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $first_equipments->links() }}
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
                    url: 'first_equipment_money/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.first_money_eq_id + '</td><td>' +
                                item.equipment_name + '</td><td>'+
                                new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(item.money_amount)+ '</td><td>'+
                                item.date + '</td><td>'+
                                item.description + '</td><td id="operations">'+
                                '<button  id-item=' + item.first_money_eq_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_first_equipment" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.first_money_eq_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_first_equipment" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('.loading').hide();
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_first_equipment", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("first_equipment/update/" + id);
            });

            // Delete first_equipment
            $("#content-display").on('click', ".delete_first_equipment", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("first_equipment/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>

