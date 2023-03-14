
<div class="col-md-12">
	
	<h2 style="text-align: center"> لیست علت مصرف</h2>

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
			<th>ادی</th>
			<th>نوع مصرف</th>
			
			<th id="operations_title">عملیات</th>
		
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th>ادی</th>
			<th>نوع مصرف</th>
			
			<th id="operations_title">عملیات</th>
		
		</tr>
		</tfoot>
		<tbody id="content-display">
		@foreach($reason_pays as $reason_pay)
			<tr>
				<td>{{$reason_pay->expense_reason_id}}</td>
				<td>{{$reason_pay->title}}</td>
				
				
				<td id="operations"><a href="javascript:ajaxLoad('reason_pay/update/{{$reason_pay->expense_reason_id}}')" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-edit"></i></a>
					<a href="javascript:if(confirm('Are you sure you want to delete this?')) ajaxDelete('{{route('reason_pay.delete',$reason_pay->expense_reason_id)}}','{{csrf_token()}}')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
			</tr>
		
		@endforeach
		
		</tbody>
	</table>
	<div class="pagination" style="float: left">
		{{ $reason_pays->links() }}
	</div>

	<script type="text/javascript">

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
                        url: 'reason_pay/search/'+search,
                        success: function (response) {
                            var trHTML = '';

                            $.each(response.data, function (i, item) {
                                trHTML += '<tr><td>' + item.expense_reason_id + '</td><td>' +
                                    item.title + '</td><td id="operations">'+
                                    '<button  id-item=' + item.expense_reason_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-sm delete_reason_pay" ></button>' + '&nbsp;' +
                                    '<button  id-item=' + item.expense_reason_id + ' class="glyphicon glyphicon-edit btn btn-info btn-sm edit_reason_pay" ></button>' +
                                    '</td>'
                                '</tr>';

                            });
                            $('.loading').hide();
                            $('#content-display').html(trHTML)
                        }

                    })
                }

                //edit detail section
                $("#content-display").on('click', ".edit_reason_pay", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    javascript:ajaxLoad("reason_pay/update/" + id);
                });

                // Delete reason_pay
                $("#content-display").on('click', ".delete_reason_pay", function () {

                    var id = $(this).attr("id-item");
                    //alert(id)
                    if (confirm('Are you want to delete this record?')){
                        javascript:ajaxDelete("reason_pay/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
                    }
                });

            })
        })
	</script>



</div>







