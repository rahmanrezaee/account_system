<h4 class="" style="text-align: center">
    {{isset($panel_title) ?$panel_title :''}}
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="form-group col-md-3" style="text-align: right">

            <input class="form-control" name="customer_code" id="customer_code" placeholder="کد مشتری را وارد کنید!">
            {{--<select name="customer_id" id="customer_id" class="form-control select2_1 dynamic" data-state="company">--}}
                {{--<option value="0" disabled="true" selected="true">انتخاب مشتری</option>--}}
                {{--@foreach($customers as $customer)--}}
                    {{--<option value="{{$customer->customer_id}}">{{$customer->name}}</option>--}}
                {{--@endforeach--}}
                {{--{{csrf_token()}}--}}
            {{--</select>--}}
        </div>


        <div class="form-group col-md-2  col-lg-offset-3 text-right">
            <button id="get-report" type="button" class="btn btn-info btn-rounded btn-sm"> گرفتن گزارش&nbsp;<i
                        class="report_icon"></i></button>
        </div>
        <div class="form-group col-md-2  text-left">

        </div>
        <div class="col col-md-2">
            <button class="btn btn-info btn-rounded pull-left" onclick="imprimir()">چاپ کردن</button>
            {{--<div class="form-group">--}}
                {{--<select class="list-page form-control">--}}
                    {{--<option>select count</option>--}}
                    {{--<option>5</option>--}}
                    {{--<option>10</option>--}}
                    {{--<option>20</option>--}}
                    {{--<option>50</option>--}}
                {{--</select>--}}
            {{--</div>--}}
        </div>
    </div>
</div>
<div class="col-md-12">

    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>شماره</th>

            <th>شماره فاکتور</th>
            <th>تاریخ خرید</th>
            <th>تخفیف</th>
            <th>پرداختی فعلی</th>
            <th>باقی</th>
            <th>پرداختی کل</th>
            <th>علمیات</th>


        </tr>
        </thead>
        <tbody id="factor_report">

        </tbody>
        <tfoot id="factor_total">

        </tfoot>
    </table>
    <div class="pagination" style="float: left" id="pagination">

    </div>
</div>


@include('sale_factor.month')
@include('sale_factor.year')
@include('sale_factor.bettwen_date')


<div id="myModal" class="modal" style="top: 100px" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">پرداخت باقیات فکتور</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">

                <form method="post" class="margin-bottom-20" id="frm" action="{{ route('buyFactor.paymentCustomer') }}">
                    {{csrf_field()}}

                    <input type="hidden" name="sale_factor_id" id="sale_factor_id">
                    <div class="col-md-4">
                        <div class="form-group required  margin-bottom-20" id="form-payment_amount-error">
                            <label for="payment_amount">مقدار قابل پرداخت:*</label>
                            <!-- /.input-group-btn -->
                            <input type="hidden" name="factor_id" id="factor_id">
                            <input type="text" class="form-control required col-md-12" id="payment_amount"
                                   name="payment_amount"
                                   placeholder="مقدار بدهی :*">
                            <span style="font-size: 25px;" id="payment_amount-error" class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group margin-bottom-20">
                            <label for="end_date" class="control-label ">تاریخ:*</label>
                                <input type="date"  value="" class="form-control " placeholder="روز/ماه/سال"
                                       name="date_payment">

                        </div>
                    </div>
                    <div class="form-group col-md-4 col-lg-4 col-sm-12 required">
                        <label for="stack_name">نرخ ارز و ارز:*</label>

                        <div class="input-group d-flex">
                            <input type="text"
                                   value=""
                                   required name="currency_rate"
                                   id="currency_rate"
                                   class="form-control select2_1 sale_factor_code">

                            <select class="form-control no-padding" name="currency_id" id="currency_id">

                            </select>
                        </div>
                    </div>
                    <div class="co-md-12">
                        <div class="form-group">

                            <button class="btn btn-primary margin-top-20" type="submit" id="btn_save">ذخیره<i
                                        class="fa fa-save" style="margin-right: 5px;"></i>
                            </button>
                            <button type="button" class="btn btn-secondary margin-top-20" data-dismiss="modal">لغو
                            </button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>


<div id="showDetail" class="modal" style="top: 100px" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center" style="justify-content: space-between">
                <h3 class="modal-title " style="flex-grow: 0.8">لیست باقیات فکتور</h3>
                <button type="button" class="btn btn-link btn-sm " onclick="printPaymentList()">
                    چاپ
                    <i class="fa fa-print"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">

                <table id="Detailtable" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>شماره</th>
                        <th>پرداختی</th>
                        <th>واحد پول</th>
                        <th>تاریخ</th>
                    </tr>

                    </thead>
                    <tfoot id="footer-total-payment">

                    </tfoot>
                    <tbody id="payment-report">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="/js/jquery-ui.min.js"></script>

<script>

    $(document).ready(function () {

        $(document).off('click').on('click', '#get-report', function () {

            var customer_code = $("#customer_code").val();
            var _token = $('input[name="_token"]').val();

            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();

            $.ajax({
                url: "{{ route('searchFactorForCustomerPay') }}",
                method: 'post',
                data: {_token: _token, customer_code: customer_code},
                dataType: 'json',
                cache: false,
                async:false,
                success: function (response) {
                    var data = response['data'].data;
                    var table = "";

                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            var remind = data[i].total_price - (data[i].recieption_price + data[i].discount);
                            table += '<tr>' +
                                '<td>' + data[i].sale_factor_id + '</td>' +
                                '<td>' + data[i].sale_factor_code + '</td>' +
                                '<td>' + data[i].sale_date + '</td>' +
                                '<td>' + data[i].discount + '</td>' +
                                '<td>' + data[i].recieption_price + '</td>' +
                                '<td>' + remind + '</td>' +
                                '<td>' + data[i].total_price + '</td>' +
                                '<td>' +
                                '<button currency_name="'+data[i].currency_name+'" currency_rate="'+data[i].currency_rate+'" remind=' + remind + ' id-factor = ' + data[i].sale_factor_id + ' title="پرداخت باقیات" class="btn btn-circle bg-lightdark payment-detail btn-link btn-xs"><i class="fa fa-money"></i></button>' +
                                '<button id-factor=' + data[i].sale_factor_id + ' data-name="'+data[i].customer_name+'"  remind=' + remind + ' title="دیدن جزییات" class="btn payment-list btn-circle  bg-lightdark btn-link btn-xs" ><i class="fa fa-eye"></i></button>'
                                + '</td>' +
                                '</tr>';

                        }
                        var footer = "<tr><td></td><td></td><td></td><td>مجموعه کل: </td><td>"
                            + response.totalpayment +
                            "</td><td>مجموعه باقی ماند: </td><td>" + response.remind + "</td><td></td></tr>";
                    } else {
                        table += '<tr><td colspan="8" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                    }

                    $(".report_icon").removeClass('fa fa-spinner fa-spin');
                    $('.loading').hide();

                    $('#factor_report').html(table);
                    $('#pagination').html(response['pagination']);

                    $("#factor_total").html(footer);

                },
                error: function (xhr) {
                    alert(xhr.responseText);

                }

            })

        })

        $(document).on("click", ".payment-detail", function () {

            var remind = $(this).attr("remind");
            var id_factor = $(this).attr("id-factor");
            var currency_name = $(this).attr("currency_name");
            var currency_rate = $(this).attr("currency_rate");

            $("#sale_factor_id").val(id_factor);
            $("#payment_amount").val(remind);
            $("#currency_id").html('<option>'+currency_name+'</option>');
            $("#currency_rate").val(currency_rate);

            $("#myModal").modal("show")


        })

        $(document).on("click", ".payment-list", function () {

            var remind = $(this).attr("remind");
            var id_factor = $(this).attr("id-factor");

            $.ajax({
                type: 'GET',
                cache: false,
                async:false,
                url: 'customer/paymentList/' + id_factor,
                dataType: 'json',
                success: function (response) {
                    var data = response.data;
                    var table = "";
                    var footer = "";
                    if (data  !=  null){
                    for (var i = 0; i < data.length; i++) {
                        table += '<tr>' +
                            '<td>' + (i+1) + '</td>' +
                            '<td>' + data[i].payment_amount + '</td>' +
                            '<td>' + data[i].currency_name + '</td>' +
                            '<td>' + data[i].date + '</td>' +
                            '</tr>';




                    }
                    }else {
                        table += '<tr>' +
                            '<td colspan="3">دیتا موجود نیست</td>' +
                            '</tr>';
                    }

                    footer += "<tr>" +
                        "<td > مجموعه پرداختی: </td>" +
                        "<td >"+ response.total+" "+data[0].currency_name +"</td>" +
                        "<td > باقی مانده:</td>" +
                        "<td >"+ remind+" "+data[0].currency_name +"</td>" +
                        "</tr>"

                    $('#payment-report').html(table);
                    $('#footer-total-payment').html(footer);
                    $("#showDetail").modal("show")
                },
                error: function () {

                    alert("error occur")

                }
            });


        })
    });

    $(document).ready(function () {


        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });


        /*================
   JALALI DATEPICKER
   * ===============*/
        var opt = {

            // placeholder text

            placeholder: "",

            // enable 2 digits

            twodigit: true,

            // close calendar after select

            closeAfterSelect: true,

            // nexy / prev buttons

            nextButtonIcon: "fa fa-forward",

            previousButtonIcon: "fa fa-backward",

            // color of buttons

            buttonsColor: "پیشفرض ",

            // force Farsi digits

            forceFarsiDigits: true,

            // highlight today

            markToday: true,

            // highlight holidays

            markHolidays: false,

            // highlight user selected day

            highlightSelectedDay: true,

            // true or false

            sync: false,

            // display goto today button

            gotoToday: true

        };


        kamaDatepicker('jalali-datepicker', opt);
        kamaDatepicker('jalali-enddate', opt);
        kamaDatepicker('jalali-startdate', opt);
        kamaDatepicker('tat', opt);

        /*================
          EDTITABEL TABLE
        * ===============*/
        $('.select2_1').select2();

    });
    /** customer report js**/
    $(function () {
        $('#as_date').hide();
        $('#year').hide();
        $('#month').hide();
        $('#between_date').hide();
        $('#type').change(function () {

            if ($('#type').val() == 'month') {
                $('#between_date')
                $('#year').hide();
                $('#as_date').hide();
                $('#month').show();
            } else if ($('#type').val() == 'week') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'day') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'year') {
                $('#month').hide();
                $('#as_date').hide();
                $('#year').show();
            } else if ($('#type').val() == 'bt_date') {
                $('#month').hide();
                $('#year').hide();
                $('#as_date').show();
                $('#between_date').show();
            } else {
                $('#selection').hide();
            }
        });
    });

    // Pagination
    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var url = $(this).attr("href");

        var customer_id = $("#customer_id").val();
        var _token = $('input[name="_token"]').val();
        $('.loading').show();

        $.ajax({
            url: url,
            method: 'post',
            async:false,
            cache: false,
            data: {_token: _token, customer_id: customer_id},
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                for (var i = 0; i < data.length; i++) {
                    var remind = data[i].total_price - (data[i].recieption_price + data[i].discount);
                    table += '<tr>' +
                        '<td>' + data[i].sale_factor_id + '</td>' +
                        '<td>' + data[i].sale_factor_code + '</td>' +
                        '<td>' + data[i].sale_date + '</td>' +
                        '<td>' + data[i].discount + '</td>' +
                        '<td>' + data[i].recieption_price + '</td>' +
                        '<td>' + remind + '</td>' +
                        '<td>' + data[i].total_price + '</td>' +
                        '<td>' +
                        '<button remind=' + remind + ' id-factor = ' + data[i].sale_factor_id + ' title="پرداخت باقیات" class="btn btn-circle bg-lightdark payment-detail btn-link btn-xs"><i class="fa fa-money"></i></button>'
                        +
                        '<button id-factor=' + data[i].sale_factor_id + ' title="دیدن جزییات" class="btn btn-circle  bg-lightdark btn-link btn-xs payment-list" ><i class="fa fa-eye"></i></button>'
                        + '</td>' +
                        '</tr>';
                }
                $('.loading').hide();
                $('#factor_report').html(table);
                $('#pagination').html(response['pagination']);

                var footer = "<tr><td></td><td></td><td></td><td>مجموعه کل: </td><td>"
                    + response.totalpayment +
                    "</td><td>مجموعه باقی ماند: </td><td>" + response.remind + "</td><td></td></tr>";


                $("#factor_total").html(footer);

            }
        });
    })

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var customer_id = $("#customer_id").val();
        var _token = $('input[name="_token"]').val();

        $('.loading').show();

        $.ajax({
            url: "{{ route('searchFactorForCustomerPay') }}" + "/" + $(this).val(),
            method: 'post',
            cache: false,
            async:false,
            data: {_token: _token, customer_id: customer_id},
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        var remind = data[i].total_price - (data[i].recieption_price + data[i].discount);
                        table += '<tr>' +
                            '<td>' + data[i].sale_factor_id + '</td>' +
                            '<td>' + data[i].sale_factor_code + '</td>' +
                            '<td>' + data[i].sale_date + '</td>' +
                            '<td>' + data[i].discount + '</td>' +
                            '<td>' + data[i].recieption_price + '</td>' +
                            '<td>' + remind + '</td>' +
                            '<td>' + data[i].total_price + '</td>' +
                            '<td>' +
                            '<button remind=' + remind + ' id-factor = ' + data[i].sale_factor_id + ' title="پرداخت باقیات" class="btn btn-circle bg-lightdark payment-detail btn-link btn-xs"><i class="fa fa-money"></i></button>'
                            +
                            '<button id-factor=' + data[i].sale_factor_id + ' title="دیدن جزییات" class="btn payment-list btn-circle  bg-lightdark btn-link btn-xs" ><i class="fa fa-eye"></i></button>'
                            + '</td>' +
                            '</tr>';
                    }
                    var footer = "<tr><td></td><td></td><td></td><td>مجموعه کل: </td><td>"
                        + response.totalpayment +
                        "</td><td>مجموعه باقی ماند: </td><td>" + response.remind + "</td><td></td></tr>";
                } else {
                    table += '<tr><td colspan="8" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                }

                $('.loading').hide();

                $('#factor_report').html(table);
                $('#pagination').html(response['pagination']);

                $("#factor_total").html(footer);

            },
            error: function (xhr) {
                alert(xhr.responseText);

            }
        })

    })


    $(document).ready(function () {


        $('#currency_id').change(function () {

            var _token = $('input[name="_token"]').val();

            $.ajax({

                cache: false,
                async:false,
                url: "{{route('sale.currencyExchanger')}}",
                method: 'get',
                dataType: 'json',
                data: {_token: _token, currency_id: $(this).val()},
                success: function (result) {

                    $("#currency_rate").val(result)
                }


            });

        });


    })


</script>


<script type="text/javascript">
    function imprimir() {

        var customer_name = $(".payment-list").attr('data-name');

        console.log(customer_name);
        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} th:last-child,td:last-child{display: none;} body{font-family:sahle}</style>" +
            "</head><body style='direction: rtl;font-family:sahel'>" + "<h1 class='text-center'> گزارش باقیات : " + customer_name + " </h1>" + table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        }, 3000)


    }

    function printPaymentList() {

        var stock = $("#customer_id option:selected").text();
        var customer_name = $(".payment-list").attr('data-name');

        console.log(customer_name);

        var table = document.getElementById("Detailtable");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} body{font-family:sahle}</style>" +
            "</head><body style='direction: rtl;font-family:sahel'>" + "<h3 class='text-center'> گزارش باقیات : " + customer_name + " </h1>" + table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        }, 3000)


    }
</script>