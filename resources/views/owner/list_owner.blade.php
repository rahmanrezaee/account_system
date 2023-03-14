<div class="col-md-12">
    @include('notifications.notifications');
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :'لیست شرکا'}}
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
            <th >ای دی</th>
            <th >نام و تخلص</th>
            <th >سهم شراکت</th>
            <th >تاریخ شراکت</th>
            <th id="operations_title" >عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">
            @foreach ($owners as $owner)
                <tr>
                    <td>{{$owner->owner_id}}</td>
                    <td>{{$owner->full_name}}</td>
                    <td>{{$owner->percentage}}</td>
                    <td>{{$owner->date_share}}</td>
                    <td id="operations">
                        <a href="javascript:ajaxLoad('{{route('owner.update',$owner->owner_id)}}')"><i class="glyphicon glyphicon-edit btn btn-primary btn-sm"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="pagination" style="float: left">
        {{ $owners->links() }}
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
                        url: 'owner/search/'+search,
                        success: function (response) {
                            var trHTML = '';

                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.owner_id + '</td><td>' +
                                    item.full_name + '</td><td>'+ item.percentage + '</td><td>'+
                                    item.date_share + '</td><td id="operations">'+
                                    '<button  id-item=' + item.owner_id + ' class="glyphicon glyphicon-edit btn btn-primary btn-sm edit_owner" ></button>'
                                    '</td>'
                                '</tr>';

                            });
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_owner", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("owner/update/" + id);
                });

                // Delete owner
                $("#content-display").on('click', ".delete_owner", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("owner/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })
        })
    </script>
</div>

