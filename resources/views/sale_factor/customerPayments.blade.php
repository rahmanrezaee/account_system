<div class="col-md-12">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :''}}
    </h4>
    <div class="row margin-bottom-10">
        <div class="col-md-1">
            <div class="input-group">
                <button id="print_button" onclick="printPayement()"  class="fa fa-print form-control" ></button>
            </div>
        </div>
        <div class="col-md-10"></div>

    </div>
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>کد نمبر فاکتور</th>
            <th>مقدار</th>
            <th>تاریخ</th>
        </tr>
        </thead>
        <tbody id="content-display">
        @foreach($customerPayments as $key=>$cp)
            <tr>
                <td>{{++$key}}</td>
                <td>{{$cp->sale_factor_id}}</td>
                <td>{{$cp->payment_amount . ' ' . $currency}}</td>
                <td>{{$cp->date}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th>
                محموعه پرداختی ها
            </th>
            <th colspan="3" class="text-center">
                {{ $total }}
            </th>
        </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">

//    $(document).ready(function () {
//        $('#print_button').on('click',function(){
//            window.print()
//        })
//    })

    function printPayement() {

        // var stack = $("#stack_name option:selected").text();

        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} body{font-family:sahle}</style>"+
            "</head><body style='direction: rtl;font-family:sahel'>"+
            "<h3 class='text-center'>لیست پرداختی های فاکتور  </h3>" +
                "<div style='display:flex;justify-content: space-around;font-size:16px'>" +
            "<span> نام مشتری: {{ $cp->customer_name }}" + " </span>" +
            "<span> کد فاکتور: {{ $cp->sale_factor_id }}" + " </span>" +
            "</div>"+
            ""+ table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        },3000)



    }
</script>

