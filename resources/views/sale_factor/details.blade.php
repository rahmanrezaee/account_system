<div class="col-lg-12 col-md-12 col-sm-12">

    <h3 class="text-center"> <tr>


         جزئیات فکتورهای خارج شده گدام
            <a href="javascript:ajaxLoad('sale_factor')"
               class="btn btn-sm btn-bordered glyphicon glyphicon-backward">برگشت</a></h3>

    <table id="example" class="table table-striped table-bordered display"  style="width:100%">
        <thead>
            <tr>
                <th>نام محصول</th>
                <th>قیمت </th>
                <th>تاریخ </th>
                <th>تعداد محصول</th>
                <th>نمبرگدام</th>
                <th> ملاحظات</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="2"><h3>مجموعه : </h3></td>
                <td colspan="3"><h3>{{ $sale_title->total_price }} </h3></td>
            </tr>
        </tfoot>
        <tbody>

        @foreach($sale_factors as $sale_factor)

            <tr>
                <td>{{$sale_factor->product_name}}</td>
                <td>{{ number_format($sale_factor->package_price) }}</td>
                <td>{{$sale_factor->sale_date}}</td>
                <td>{{$sale_factor->quantity}}</td>
                <td>{{$sale_factor->store_id}}</td>
                <td></td>
            </tr>

        @endforeach

        </tbody>
    </table>

</div>

<script>

    $(document).ready(function () {

        table = $('#example').DataTable({
            destroy: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    footer: true,
                    customize: function (win) {
                        $(win.document.body)
                            .css({"direction": "rtl"})
                        $(win.document.body).find('h1').css("textAlign", "center");

                        let titleSelect = $(win.document.body).find('h1');
                        titleSelect.append("<hr>");
                        titleSelect.append(
                            "<div style='text-align: right;font-size: 18px'>" +

                                "<span style='margin-right: 30px;margin-left: 10px'> مشتری : "
                                + "{!! $sale_title->cname !!}" +
                                " </span>  " +
                                "<span style='margin-right: 30px;margin-left: 10px'> تاریخ : "
                                + "{!! $sale_title->sale_date !!}" +
                                " </span>  "
                                +

                            '</div>')

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');

                    },

                     title: '{!! isset($panel_title) ?$panel_title :'' !!}',


                },
                {
                    extend: 'excelHtml5',
                    autoFilter: true,
                    footer: true,
                    sheetName: 'Exported data'
                }
            ],

        });

    })


</script>