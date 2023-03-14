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
            <th>اسم نماینده گی</th>
            <th>موقعیت نماینده گی</th>
            <th>آدرس نماینده گی</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">
            @foreach($agencies as $agency)
            <tr>
                <td>{{$agency->agency_id}}</td>
                <td>{{$agency->agency_name}}</td>
                <td>{{$agency->location}}</td>
                <td>{{$agency->address}}</td>
                <td id="operations">
                    <a href="javascript:ajaxLoad('{{route('agency.update',$agency->agency_id)}}')"><span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span></a >
                    <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('agency.delete',$agency->agency_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $agencies->links() }}
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
                    url: 'agency/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.agency_id + '</td><td>' +
                                item.agency_name + '</td><td>'+
                                item.location + '</td><td>'+
                                item.address + '</td><td id="operations">'+
                                '<button  id-item=' + item.agency_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_agency" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.agency_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_agency" ></button>' +
                                '</td>'
                            '</tr>';

                        });
                        $('.loading').hide();
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_agency", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("agency/update/" + id);
            });

            // Delete car
            $("#content-display").on('click', ".delete_agency", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("agency/delete/" + id , $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>

</div>

