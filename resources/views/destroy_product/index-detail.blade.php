
<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>شماره</th>
            <th>محصول</th>
            <th>قیمت پکیچ</th>
            <th>قیمت واحد</th>
            <th>قمیت کل</th>
            <th>تعداد</th>
            <th>تاریخ حذف</th>




        </tr>
        </thead>
        <tbody>
            @foreach($detail as $key=>$det)
                <tr>
                    <td>{{++$key}}</td>
                    <td>{{$det->product_name}}</td>
                    <td>{{$det->package_price}}</td>
                    <td>{{$det->unit_price}}</td>
                    <td>{{$det->total_price}}</td>
                    <td>{{$det->quantity}}</td>
                    <td>{{$det->date}}</td>
                </tr>
                @endforeach

        </tbody>


    </table>
    <a href="javascript:ajaxLoad('{{route('destroy_product.list')}}')" class="btn btn-default">برگشت</a>

</div>
<script>
    $(document).ready(function () {
        datatable();
    })
</script>

