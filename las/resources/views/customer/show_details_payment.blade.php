<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
{{--<p id="message" style="height: 100px;width:300px;"></p>--}}
<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >ای دی</th>
            <th >ازبابت</th>
            <th >تاریخ</th>
            <th >مقدارپرداخت (افغانی)</th>
            <th >توضیحات</th>
            <th >عملیات</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payment as $pay)
            <tr>
                <td>{{$pay->reserve_payment_id}}</td>
                <td>{{$pay->reserve_type}}</td>
                <td>{{$pay->date}}</td>
                <td>{{$pay->payment_amount}}</td>
                <td>{{$pay->description}}</td>

                <td colspan="2">
                    <a href="javascript:ajaxLoad('{{route('customer.details_payment_update',$pay->reserve_payment_id)}}')"><i class="glyphicon glyphicon-edit btn btn-primary btn-sm"></i></a>
                </td>
            </tr>

        @endforeach
        <tr>

        <td>
            <a href="javascript:ajaxLoad('{{route('customer_show_payment',$pay->reserve_payment_id)}}')"><i class="glyphicon glyphicon-backward btn btn-default btn-sm">برگشت</i></a>
        </td>
            <td colspan="5">
                <h3> مجموعه پرداخت : {{$total}}</h3>
            </td>
        </tr>
        </tbody>

    </table>
    <script>
        $(document).ready(function () {
            datatable();
        })
    </script>
</div>

