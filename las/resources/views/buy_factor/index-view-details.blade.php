
<div class="col-md-12 ">
    <h4 class="" style="text-align: center">
        {{isset($panel_title) ?$panel_title :'اجناس شامل فکتور'}}
    </h4>
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>نام محصول</th>
            <th> قیمت خرید (افغانی)</th>
            {{--<th >قیمت فروش</th>--}}
            <th >تعداد</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bproducts as $key=>$bproduct)
            <tr>
                <td>{{++$key}}</td>
                <td>{{$bproduct->product_name}}</td>
               {{-- <td>{{$bproduct->sale_price}}</td>--}}
                <td>{{number_format($bproduct->buy_price)}}</td>
                <td>{{$bproduct->quantity}}</td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <a href="javascript:ajaxLoad('{{route('buy_factor.list')}}')" class="btn btn-default">برگشت</a>
</div>
<script>
    $(document).ready(function () {
       datatable();
    })
</script>

