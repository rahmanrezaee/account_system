
<h4 style="text-align: center">
    {{isset($panel_title) ? $panel_title :''}}
</h4>
<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>فرستنده</th>
            <th>دریافت کننده</th>
            <th>مقدار</th>
            <th>واحد</th>
            <th>نرخ</th>
            <th>تاریخ</th>
            <th>توضیحات</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>

        @foreach($moneyStores as $money)
            <tr>
                <td>{{$money->money_store_id}}</td>
                <td>{{$money->full_name}}</td>
                <td>{{$money->payment_amount}}</td>
                <td>{{$money->payment_type}}</td>
                <td>{{$money->payment_status}}</td>
                <td>
                    <a href="javascript:ajaxLoad('{{route('money_store.update',$money->money_store_id)}}')"><span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span></a >
                    <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('money_store.delete',$money->money_store_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                    <a href="javascript:if(confirm('آیامیخواهید این حساب را تصفیه کنید؟')) ajaxDelete('{{route('money_store.payment_status',$money->money_store_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-tasks" title="تصفیه حساب"></i></span></a>
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

