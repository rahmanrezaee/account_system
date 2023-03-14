
<div class="col-md-12">


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
            <th>نام</th>
            <th>تخلص</th>
            <th>معاش</th>
            <th>تلفن</th>
            <th>آدرس</th>
            <th>عملیات</th>
        
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ادیِ</th>
            <th>نام</th>
            <th>تخلص</th>
            <th>معاش (افغانی)</th>
            <th>تلفن</th>
            <th>آدرس</th>
            <th id="operations_title"> عملیات</th>
        
        </tr>
        </tfoot>
        <tbody id="content-display">

        @foreach($employees as $emp)
            <tr>
                <td>{{$emp->employee_id}}</td>
                <td>{{$emp->first_name}}</td>
                <td>{{$emp->last_name}}</td>
                <td>{{ number_format($emp->salary) .' '. getCurrency($emp->currency_id) }}</td>
                <td>{{$emp->phone}}</td>
                <td>{{$emp->address}}</td>

                <td id="operations">
{{--                    <a href="javascript:ajaxLoad('{{route('employee.showDetail',$emp->employee_id)}}')" class="btn btn-info btn-xs" id="show_details">نمایش جزئیات--}}</a>
                    <a href="javascript:ajaxLoad('{{route('employee.update',$emp->employee_id)}}')" class="glyphicon glyphicon-edit btn btn-success btn-xs" id="edit_employee"></a>
                    <a href="javascript:if(confirm('Do you want delete this record?'))ajaxDelete('{{route('employee.delete',$emp->employee_id)}}','{{csrf_token()}}')" class="glyphicon glyphicon-trash btn btn-danger btn-xs" id="delete_employee"></a>
                </td>
            </tr>
        @endforeach
        
        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $employees->links() }}
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
                        url: 'employee/search/'+search,
                        success: function (response) {
                            var trHTML = '';
                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.employee_id + '</td><td>' +
                                    item.first_name + '</td><td>'+
                                    item.last_name + '</td><td>'+
                                    item.salary + '</td><td>'+
                                    item.phone + '</td><td>'+
                                    item.address + '</td><td id="operations">'+
                                    '<button  id-item=' + item.employee_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_employee" ></button>' + '&nbsp;' +
                                    '<button  id-item=' + item.employee_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_employee" ></button>' +
                                    '</td>'
                                '</tr>';

                            });
                            $('.loading').hide();
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_employee", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("employee/update/" + id);
                });

                // Delete employee
                $("#content-display").on('click', ".delete_employee", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("employee/delete/" + id , $('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })
        })
    </script>
</div>