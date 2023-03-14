
<h4 style="text-align: center">
    {{isset($panel_title) ? $panel_title :''}}
</h4>
<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>نوعیت حساب</th>
            <th>مقدار پول (افغانی)</th>
            <th>توضیحات</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>

         @foreach($revenues as $revenue)
            <tr>
                <td>{{$revenue->revenue_id}}</td>
                <td>{{$revenue->store['name']}}</td>
                <td>{{ number_format($revenue->money_amount) }}</td>
                <td>{{$revenue->description}}</td>
                <td>
                    <a href="javascript:ajaxLoad('{{route('revenue.update',$revenue->revenue_id)}}')"><span><i class="glyphicon glyphicon-edit btn btn-success btn-sm" title="ویرایش" ></i></span></a >
                    <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('revenue.delete',$revenue->revenue_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash btn btn-danger btn-sm"></i></span></a>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

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
        getModalId();

    })
</script>

