<div class="row">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :'گزارش نقل و انتقالات پول'}}
    </h4>
    <form action="{{route('money_store.resumecha')}}" id="report">
        <div class="col col-md-12" style="display: flex; align-items: flex-end">

            {{ csrf_field() }}

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="state">انتخاب حالت گزارش:*</label>
                    <select name="state" id="state" class="form-control select2_1">
                        <option value="customerPayment">خرید فروش</option>
                        <option value="expense"> مصارف</option>
                        <option value="employee"> پرداخت کارمندان</option>
                        <option value="transferMoney"> انتقال پول بین حسابات</option>
                    </select>
                </div>
            </div>

            <div class="col col-md-2">
                <div class="form-group ">
                    <label for="startdate" class="control-label ">از تاریخ:*</label>

                    <input type="date" class="form-control " placeholder="روز/ماه/سال" id="startdate"
                           name="start_date">

                </div>
            </div>
            <div class="col col-md-2">
                <div class="form-group " id="between_date">
                    <label for="enddate" class="control-label "> تا تاریخ:*</label>
                    <input type="date" class="form-control" placeholder="روز/ماه/سال" id="enddate"
                           name="end_date">

                </div>
            </div>

            <div class="col col-md-3">
                <div class="form-group ">
                    <div class="d-flex">
                        <button class="btn btn-info btn-rounded" id="report-btn"> گزارش&nbsp;<i class="report_icon"></i>
                        </button>
                        <button class="btn btn-primary btn-rounded width-100 form-control" onclick="imprimir(event)">
                            پرنت <i
                                    class="fa fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>


        </div>

    </form>
    <div class="col-md-12">


    </div>
    <div class="col-md-12">

        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead>
            <tr>

                <th>شماره</th>
                <th>نام حساب</th>
                <th>تاریخ ثبت</th>
                <th>توضیحات</th>
                <th>مبلغ اصلی</th>
                <th>ارز اصلی</th>
                <th>نرخ ارز اصلی</th>
                <th>مبلغ پرداخت شده</th>
                <th>نرخ ارز پرداخت شده</th>
                <th>نرخ ارز پرداخت شده</th>
                <th>حساب پرداخت شده</th>
            </tr>
            </thead>
            <tfoot id="content-footer">

            </tfoot>
            <tbody id="content-display">

            </tbody>
        </table>

    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        $('#report-btn').on('click', function (e) {

            e.preventDefault();
            var data = $("#report").serialize();
            var url = $("#report").attr('action');

            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {


                    console.log(response);

                    var data = response[0];

                    var footer = response[1];

                    let htmlFooter = '<tr><th>مبلغ پرداختی : </th>';

                    for (var k in footer.drawer_money) {

                        htmlFooter += "<th>" + footer.drawer_money[k] + "</th> <th>" + k + "</th>"

                    }
                    htmlFooter += '</tr>';

                    htmlFooter += '<tr><th>مبلغ دریافتی : </th>';

                    for (var k in footer.receiver_money) {

                        htmlFooter += "<th>" + footer.receiver_money[k] + "</th> <th>" + k + "</th>"

                    }
                    htmlFooter += '</tr>';

                    htmlFooter += '<tr><th>مبلغ کل : </th>';

                    for (var k in footer.total_money) {

                        htmlFooter += "<th>" + footer.total_money[k] + "</th> <th>" + k + "</th>"

                    }
                    htmlFooter += '</tr>';

                    var table = "";

                    if (data.length > 0) {

                        for (var i = 0; i < data.length; i++) {

                            table += '<tr>' +
                                '<td>' + i + '</td>' +
                                '<td>' + data[i].account_name + '</td>' +
                                '<td>' + data[i].payment_date + '</td>' +
                                '<td>' + data[i].description + '</td>' +
                                '<td>' + data[i].mount + '</td>' +
                                '<td>' + data[i].currency + '</td>' +
                                '<td>' + data[i].rate + '</td>' +
                                '<td>' + data[i].mountexchanged + '</td>' +
                                '<td>' + data[i].rateexchanged + '</td>' +
                                '<td>' + data[i].currencyexchanged + '</td>' +
                                '<td>' + data[i].accountexchanged + '</td>' +
                                '</tr>';
                        }

                        $('.loading').hide();
                        $(".report_icon").removeClass('fa fa-spinner fa-spin');
                        $('#content-display').html(table);

                        $('#content-footer').html(htmlFooter);


                    } else {

                        $('.loading').hide();
                        $(".report_icon").removeClass('fa fa-spinner fa-spin');
                        $('#content-display').html("<tr><td colspan='11'>دیتا موجود نیست </td></tr>");

                    }

                }
            });
        });

    });


    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });


    });


</script>

<script type="text/javascript">
    function imprimir(event) {

        event.preventDefault();
        var state = $("#state option:selected").text();
        var start = $("#startdate").val();
        var end = $("#enddate").val();


        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} body{font-family:sahle;} .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {font-size: 13px !important;padding: 4px;}</style>" +
            "</head><body style='direction: rtl;font-family:sahel'>" +
            "<h1 class='text-center'>چاپ گزارش زوزنامچه از "
            + state + " </h1>"
            +
            "</h4></div><div class='col-md-2'><h4>تاریخ گزارش : "+start+"&nbsp;"+ end+"</h4></div></div>"

            + table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();

        }, 3000)


    }
</script>