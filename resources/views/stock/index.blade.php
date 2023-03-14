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
            <th>نام گدام</th>
            <th>صندوق پول</th>
            @can('isManager')
                <th>نماینده گی</th>
            @endcan
            <th>آدرس</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">
        @foreach($stores as $key=>$store)
            <tr>
                <td>{{++$key}}</td>
                <td>{{$store->store_name}}</td>
				<td>{{$store->name}}</td>
                @can('isManager')
                    <td>{{$store->agency_name}}</td>
                @endcan
                <td>{{$store->store_address}}</td>
                <td id="operations">
                    <a href="javascript:ajaxLoad('{{ route('store.update',$store->store_id) }}')" onclick="$('#stack_modal').modal('show');" data-remodal-id="{{$store->store_id}}" class="open-modal"><span><i class="glyphicon glyphicon-edit" ></i></span></a >
                    <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('store.delete',$store->store_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="pagination" style="float: left">
        {{ $stores->links() }}
    </div>
</div>

<script type="text/javascript">

    function getModalId() {
        
        $(document).on('click','.open-modal',function () {

           var store_id=$(this).data('remodal-id');
          $.ajax({
              url:'stock/update/'+store_id,
              method:'get',
              dataType:'json',
              success:function (data) {

                  $('#store_id').val(data.store_id);
                  $('#store_name').val(data.store_name);
                  $('#store_address').val(data.store_address);

              }
          })


        })
        $('#stack_modal').modal('hide');
    }



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
                    url: 'stock/search/'+search,
                    success: function (response) {
                        var trHTML = '';

                        $.each(response.data, function (i, item) {
                            trHTML += '<tr><td>' + item.store_id + '</td><td>' +
                                item.store_name + '</td><td>'+
                                item.name + '</td>';
                            if (response.user_logged_in.user_level == 1)
                                trHTML += '<td>'+item.agency_name+'</td>';

                            trHTML+= '<td>'+item.store_address + '</td><td id="operations">'+
                                '<button  id-item=' + item.store_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_store" ></button>' + '&nbsp;' +
                                '<button  id-item=' + item.store_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_store" ></button>' +
                                '</td>'
                            '</tr>';
                        });
                        $('.loading').hide();
                        $('#content-display').html(trHTML)
                    }

                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_store", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("store/update/" + id);
            });

            // Delete store
            $("#content-display").on('click', ".delete_store", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("store/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })
    })
</script>

