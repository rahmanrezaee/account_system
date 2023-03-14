<h4 class="" style="text-align: center">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-2">
            <select name="company_id" id="company_id" class="form-control select2_1 dynamic" data-state="company">
                <option value="0" disabled="true" selected="true">انتخاب شرکت</option>
                @foreach($company as $comp)
                    <option value="{{$comp->company_id}}">{{$comp->company_name}}</option>
                @endforeach
                {{csrf_token()}}
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-info btn-rounded" id="report-btn_content"> گزارش &nbsp;<i class="report_icon"></i>
            </button>
        </div>
        <div class="col-md-5"></div>

        <div class="col-md-1">

            <button class="btn btn-primary form-control btn-rounded" onclick="printRemindPayment()"> پرنت <i
                        class="fa fa-print"></i></button>

        </div>

        <div class="col-md-2">
            <div class="form-group">
                <select class="list-page form-control">
                    <option>select count</option>
                    <option>5</option>
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>شماره</th>
            <th>شرکت</th>
            <th>شماره فاکتور</th>
            <th>تاریخ خرید</th>
            <th>ارز</th>
            <th>تخفیف</th>
            <th>پرداختی فعلی</th>
            <th>باقی</th>
            <th>پرداختی کل</th>
            <th>علمیات</th>
        </tr>
        </thead>
        <tbody id="factor_report_content">

        </tbody>
        <tfoot id="factor_total">

        </tfoot>
    </table>

    <div class="pagination" style="float: left" id="pagination">

    </div>
</div>

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

                <form method="post" id="frm" action="{{route('buy_factor_payment')}}">
                    {{csrf_field()}}

                    <div class="form-group required  margin-bottom-20" id="form-payment_amount-error">
                        <label for="payment_amount">مقدار قابل پرداخت:*</label>
                        <!-- /.input-group-btn -->
                        <input type="hidden" name="factor_id" id="factor_id">
                        <input type="text" class="form-control required col-md-12" id="payment_amount"
                               name="payment_amount"
                               placeholder="مقدار بدهی :*">
                        <span style="font-size: 25px;" id="payment_amount-error" class="help-block"></span>
                    </div>

                    <div class="form-group ">
                        <label for="stack_name">نرخ ارز و ارز:*</label>
                        <div class="input-group d-flex">
                            <input type="text" value="1" required name="currency_rate"
                                   id="currency_rate"
                                   class="form-control select2_1 sale_factor_code">
                            <select class="form-control no-padding" name="currency_id" id="currency_id">
                                @foreach($currencies as $currency)
                                    <option @if(get_options("mainCurrency")->value('option_value') == $currency->currency_id) selected
                                            @endif value="{{ $currency->currency_id }}">{{ $currency->currency_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group" id="form-pr_date-error">
                        <label for="pr_date" class="control-label ">تاریخ:*</label>
                        <input type="date" value="{{ date("Y-m-d") }}"  class="form-control required"
                               placeholder="روز/ماه/سال"
                               name="pr_date" autocomplete="off">

                    </div>

                    <div>
                        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save"
                                                                                            style="margin-right: 5px;"></i>
                        </button>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>


<script>

    function factorPayment(id) {


        $.ajax({
            url: "buy_factor/searchFactorForPay/" + id,
            method: 'get',
            dataType: 'json',
            success: function (data) {

                $('#payment_amount').val(data.total_payment - data.current_payment - data.discount);
                $('#factor_id').val(data.buy_factor_id);


                $('#myModal').modal('show')

            },
            error: function (xhr) {
                alert(xhr.responseText);

            }

        })

    }

    $(document).ready(function () {


        $('#currency_id').change(function () {

            var _token = $('input[name="_token"]').val();

            $.ajax({

                cache: false,
                async: true,
                url: "{{route('sale.currencyExchanger')}}",
                method: 'get',
                dataType: 'json',
                data: {_token: _token, currency_id: $(this).val()},
                success: function (result) {

                    $("#currency_rate").val(result)
                }

            });

        });


        $("#report-btn_content").on('click', function () {

            var company_id = $("#company_id").val();
            var _token = $('input[name="_token"]').val();

            var stock = $("#company_id option:selected").text();

            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();

            $.ajax({
                url: "{{route('searchPaymentByCompany.report')}}",
                method: 'post',
                data: {_token: _token, company_id: company_id},
                dataType: 'json',
                success: function (response) {
                    // console.log(response);
                    var data = response['data'].data;
                    var tableContent = "";

                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {

                            // if (data[i].total_payment > data[i].remind) {
                            tableContent += '<tr>' +
                                '<td>' + data[i].buy_factor_id + '</td>' +
                                '<td>' + data[i].company_name + '</td>' +
                                '<td>' + data[i].factor_code + '</td>' +
                                '<td>' + data[i].buy_date + '</td>' +
                                '<td>' + data[i].currency_name + '</td>' +
                                '<td>' + data[i].discount + '</td>' +
                                '<td>' + data[i].current_payment + '</td>' +
                                '<td>' + (data[i].total_payment - data[i].current_payment - data[i].discount) + '</td>' +
                                '<td>' + data[i].total_payment + '</td>' +
                                '<td>' +
                                '<button  onclick="factorPayment(' + data[i].buy_factor_id + ')" class=" btn btn-link btn-xs detail-button" ><i class="fa fa-paypal"> پرداخت</i></button>'
                                + '</td>' +
                                '</tr>';
                            // }
                        }
                    } else {
                        tableContent += '<tr><td colspan="10" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';
                    }
                    $(".report_icon").removeClass('fa fa-spinner fa-spin');
                    $('.loading').hide();

                    $('#factor_report_content').html(tableContent);
                    $('#pagination').html(response['pagination']);
                    // table.column(4).visible(false);
                    $(".dt-button").addClass("btn");

                },
                error: function (xhr) {
                    alert(xhr.responseText);

                }

            })

        })

    })


    $(document).ready(function () {

        selectTwo();

    });

    // Pagination
    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var url = $(this).attr("href");

        var company_id = $("#company_id").val();
        var _token = $('input[name="_token"]').val();
        $('.loading').show();

        $.ajax({
            url: url,
            method: 'post',
            data: {_token: _token, company_id: company_id},
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                for (var i = 0; i < data.length; i++) {
                    table += '<tr>' +
                        '<td>' + data[i].buy_factor_id + '</td>' +
                        '<td>' + data[i].company_name + '</td>' +
                        '<td>' + data[i].factor_code + '</td>' +
                        '<td>' + data[i].buy_date + '</td>' +
                        '<td>' + data[i].discount + '</td>' +
                        '<td>' + data[i].current_payment + '</td>' +
                        '<td>' + (data[i].total_payment - data[i].current_payment) + '</td>' +
                        '<td>' + data[i].total_payment + '</td>' +
                        '<td>' +
                        '<button  onclick="factorPayment(' + data[i].buy_factor_id + ')" class=" btn btn-link btn-xs detail-button" ><i class="fa fa-paypal"> پرداخت</i></button>'
                        + '</td>' +
                        '</tr>';
                }

                $('.loading').hide();
                $('#factor_report').html(table);
                $('#pagination').html(response['pagination']);

                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");

            }
        });
    })

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var company_id = $("#company_id").val();
        var _token = $('input[name="_token"]').val();

        $('.loading').show();

        $.ajax({
            url: "{{route('searchPaymentByCompany.report')}}" + "/" + $(this).val(),
            method: 'post',
            data: {_token: _token, company_id: company_id},
            dataType: 'json',
            success: function (response) {
                // console.log(response);
                var data = response['data'].data;
                var table = "";

                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        table += '<tr>' +
                            '<td>' + data[i].buy_factor_id + '</td>' +
                            '<td>' + data[i].company_name + '</td>' +
                            '<td>' + data[i].factor_code + '</td>' +
                            '<td>' + data[i].buy_date + '</td>' +
                            '<td>' + data[i].discount + '</td>' +
                            '<td>' + data[i].current_payment + '</td>' +
                            '<td>' + (data[i].total_payment - data[i].current_payment) + '</td>' +
                            '<td>' + data[i].total_payment + '</td>' +
                            '<td>' +
                            '<button  onclick="factorPayment(' + data[i].buy_factor_id + ')" class=" btn btn-link btn-xs detail-button" ><i class="fa fa-paypal"> پرداخت</i></button>'
                            + '</td>' +
                            '</tr>';
                    }
                } else {
                    table += '<tr><td colspan="9" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';
                }
                $('.loading').hide();

                $('#factor_report').html(table);
                $('#pagination').html(response['pagination']);
                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");

            },
            error: function (xhr) {
                alert(xhr.responseText);

            }

        })
    })

    $(document).ready(function () {
        /*================
    JALALI DATEPICKER
    * ===============*/

        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });

        $('.select2_1').select2();

    });


</script>

<script type="text/javascript">
    function printRemindPayment() {

        var company = $("#company_id option:selected").text();

        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} th:last-child,td:last-child{display: none;} body{font-family:sahle}</style>" +
            "</head><body style='direction: rtl;font-family:sahel'>" + "<h1 class='text-center'>نمایش بدهی از شرکت های فروشنده : " + company + " </h1>" + table.outerHTML + "</body></html>";

        let newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();

        }, 3000)


    }
</script>