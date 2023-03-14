<div class="row">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :''}}
    </h4>
    <form action="{{route('buy_factor.report_data')}}" id="report">
        <div class="col col-md-12 d-flex " style="align-items: center;">


            <div class="col col-md-3">
                <div class="form-group">
                    <label for="company_name">انتخاب شرکت:*</label>
                    <select name="company_name" id="company_name" class="form-control select2_1">
                        <option disabled>انتخاب شرکت</option>
                        <option value="all">همه شرکت ها</option>
                        @foreach($companies as $company)

                            <option value="{{$company->company_id}}">{{$company->company_name}}</option>

                        @endforeach
                    </select>
                </div>


            </div>
            <div class="col col-md-2">
                <div class="form-group" id="choose">
                    <label for="type">نحوه گزارش:</label>
                    <select id="type" name="type" class="form-control">
                        <option value="type-1">نوع</option>
                        <option value="day">روز</option>
                        <option value="week">هفته</option>
                        <option value="month">ماه</option>
                        <option value="year">سال</option>
                        <option value="bt_date">بین تاریخ</option>
                    </select>
                </div>


            </div>
            <div class="col col-md-2">

                @include('sale_factor.month')
                @include('sale_factor.year')
            </div>
            <div class="col col-md-3">

                @include('sale_factor.bettwen_date')
            </div>
            <div class="col col-md-3 d-flex " style="justify-content: flex-end">
                <button class="btn btn-info btn-rounded" id="report-btn"> گزارش&nbsp;<i class="report_icon"></i>
                </button>

            </div>


        </div>

    </form>
    <div class="col col-md-12 margin-bottom-20" style="align-items: center;">


        <div class="col col-md-2">
            <select class="list-page form-control">
                <option>select count</option>
                <option>5</option>
                <option>10</option>
                <option>20</option>
                <option>50</option>
            </select>



        </div>
        <div class="col col-md-4">

        </div>
        <div class="col col-md-6 d-flex " style="justify-content: flex-end">
            <button class="btn btn-primary btn-rounded" onclick="printReportFactor()"> پرنت <i class="fa fa-print"></i>
            </button>
        </div>


    </div>
    <div class="col-md-12">

        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead>
            <tr>

                <th>شرکت</th>
                <th>شماره فاکتور</th>
                <th>تاریخ خرید</th>
                <th>ارز </th>
                <th>مجموع کل</th>
                <th>باقی داری</th>
                <th>دریافتی پول</th>
                <th>تخفیف</th>

            </tr>
            </thead>
            <tfoot>
            {{--<tr>--}}
                {{--<td><h4>مجموعه باقی داری</h4></td>--}}
                {{--<td colspan="7"><h4 class="total_borrow">0</h4></td>--}}
            {{--</tr>--}}
            </tfoot>
            <tbody id="content-display">

            </tbody>
        </table>
        <div class="pagination" style="float: left" id="pagination">

        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        $('#report-btn').off('click').on('click', function (e) {

            e.preventDefault();
            var data = $("#report").serialize();
            var url = $("#report").attr('action');

            var html = '';

            var type = $("#type option:selected").text();
            if (type != "نوع") {

                if (type == "روز") {

                    html += +  new Date();

                }
                if (type == "هفته") {

                    html += getdate("week") + " " + getdate("month") + "/" +  new Date().getFullYear();

                }
                if (type == "ماه") {

                    html += $("#month_r option:selected").text() + "/" +  new Date().getFullYear();

                }
                if (type == "سال") {

                    let txt = $(".year-start").val();

                    console.log(txt);
                    let currenyear = txt.split("-")[0];
                    // $("txt").val(currenyear);
                    html += currenyear;


                }
                if (type == "بین تاریخ") {

                    html += $(".year-start").val() + " تا " + $(".year-end").val();

                }

            }
            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();

            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                dataType: 'json',
                async:false,
                cache:false,
                success: function (response) {


                    var data = response['data'].data;
                    var table = "";

                    if (data.length > 0) {

                        let totolPaymend = 0;
                        for (var i = 0; i < data.length; i++) {
                            var remind_money = data[i].total_payment - data[i].current_payment;

                            totolPaymend += remind_money;


                            table += '<tr>' +
                                '<td>' + data[i].company_name + '</td>' +
                                '<td>' + data[i].factor_code + '</td>' +
                                '<td>' + data[i].buy_date + '</td>' +
                                '<td>' + data[i].currency_name + '</td>' +
                                '<td>' + data[i].total_payment + '</td>' +
                                '<td>' + remind_money + '</td>' +
                                '<td>' + data[i].current_payment + '</td>' +
                                '<td>' + data[i].discount + '</td>' +
                                '</tr>';
                        }
                        table += '<tr><th class="text-center">مجموعه باقیات: </th><th  colspan="7" class="text-center" style="text-align: center">'+totolPaymend + ' '+ data[0].currency_name +'</th></tr>';

                    } else {
                        table += '<tr><td colspan="8" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';
                    }
                    $('.loading').hide();

                    $('#content-display').html(table);
                    $(".report_icon").removeClass('fa fa-spinner fa-spin');

                    // $('.total_borrow').text(response['total_borrow']);
                    $('.pagination').html(response['pagination']);


                    // console.log(response);

                    // $(".dt-button").addClass("btn");
                    // $('.total_borrow').text(response['total_borrow']);
                    // $('.pagination').html(response['pagination']);
                }
            });
        });

    });

    // Pagination
    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        var url = $(this).attr("href");

        var data = $("#report").serialize();
        $('.loading').show();
        $.ajax({
            type: 'GET',
            url: url,
            data: data,
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                console.log(response);

                for (var i = 0; i < data.length; i++) {
                    var remind_money = data[i].total_payment - data[i].current_payment;
                    table += '<tr>' +
                        '<td>' + data[i].company_name + '</td>' +
                        '<td>' + data[i].factor_code + '</td>' +
                        '<td>' + data[i].buy_date + '</td>' +
                        '<td>' + data[i].total_payment + '</td>' +
                        '<td>' + remind_money + '</td>' +
                        '<td>' + data[i].current_payment + '</td>' +
                        '<td>' + data[i].discount + '</td>' +
                        '</tr>';
                }
                $('.loading').hide();
                $('#content-display').html(table);

                $(".dt-button").addClass("btn");
                $('.total_borrow').text(response.total_borrow);
                $('#pagination').html(response['pagination']);
            }
        });

    });

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var data = $("#report").serialize();
        var url = $("#report").attr('action');

        var html = '';
        var companyName = $("#reason option:selected").text();

        var type = $("#type option:selected").text();
        if (type != "نوع") {

            if (type == "روز") {

                html += +  getdate();

            }
            if (type == "هفته") {

                html += getdate("week") + " " + getdate("month") + "/" +  new Date().getFullYear();

            }
            if (type == "ماه") {

                html += $("#month_r option:selected").text() + "/" +  new Date().getFullYear();

            }
            if (type == "سال") {

                let txt = $(".year-start").val();

                console.log(txt);
                let currenyear = txt.split("-")[0];
                // $("txt").val(currenyear);
                html += currenyear;


            }
            if (type == "بین تاریخ") {

                html += $(".year-start").val() + " تا " + $(".year-end").val();

            }

        }
        $('.loading').show();

        $.ajax({
            type: 'GET',
            url: url + "/" + $(this).val(),
            data: data,
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                if (data.length > 0) {

                    let totolPaymend = 0;
                    for (var i = 0; i < data.length; i++) {
                        var remind_money = data[i].total_payment - data[i].current_payment;

                        totolPaymend += remind_money;

                        table += '<tr>' +
                            '<td>' + data[i].company_name + '</td>' +
                            '<td>' + data[i].factor_code + '</td>' +
                            '<td>' + data[i].buy_date + '</td>' +
                            '<td>' + data[i].total_payment + '</td>' +
                            '<td>' + remind_money + '</td>' +
                            '<td>' + data[i].current_payment + '</td>' +
                            '<td>' + data[i].discount + '</td>' +
                            '</tr>';
                    }

                    table += '<tr><th class="text-center">مجموعه باقیات: </th><th class="text-center">'+totolPaymend+'</th></tr>';

                } else {
                    table += '<tr><td colspan="7" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';
                }
                $('.loading').hide();

                $('#content-display').html(table);

                $(".dt-button").addClass("btn");
                $('.total_borrow').text(response['total_borrow']);
                $('.pagination').html(response['pagination']);
            }
        })
    })

    $(document).ready(function () {

        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });
        selectTwo();
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
                $('#between_date').hide();
                $('#year').show();
            } else if ($('#type').val() == 'bt_date') {
                $('#month').hide();
                $('#as_date').show();
                $('#year').show();
            } else {
                $('#selection').hide();
            }
        });


        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });


    });

</script>

<script type="text/javascript">

    function printReportFactor() {


        var company = $("#company_name option:selected").text();


        var type = $("#type option:selected").text();
        var html = '';

        if (type != "نوع") {

            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();


            if (type == "روز") {



                html +=    mm + '/' + dd + '/' + yyyy;

            }
            if (type == "هفته") {

                var curr = new Date; // get current date
                var first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
                var last = first + 6; // last day is the first day + 6

                var firstday = new Date(curr.setDate(first));
                var lastday = new Date(curr.setDate(last));

                let  firstdaydate = [
                    firstday.getFullYear(),
                    ('0' + (firstday.getMonth() + 1)).slice(-2),
                    ('0' + firstday.getDate()).slice(-2)
                ].join('/');
                let lastdaydate = [
                    lastday.getFullYear(),
                    ('0' + (lastday.getMonth() + 1)).slice(-2),
                    ('0' + lastday.getDate()).slice(-2)
                ].join('/');

                html += firstdaydate+ ' - ' + lastdaydate;



            }
            if (type == "ماه") {

                html += $("#month_r option:selected").text() + "/" +  new Date().getFullYear();

            }
            if (type == "سال") {

                let txt = $(".year-start").val();

                console.log(txt);
                let currenyear = txt.split("-")[0];

                html += currenyear;


            }
            if (type == "بین تاریخ") {

                html += $(".year-start").val() + " تا " + $(".year-end").val();

            }

        }




        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} th:last-child,td:last-child{display: none;} body{font-family:sahle}</style>" +
            "</head><body style='direction: rtl;font-family:sahel'>" +
            "<h1 class='text-center'>گزارشات فکتور های خرید  : " + company + " </h1>"
            +
            "</h4></div><div class='col-md-2'><h4>تاریخ گزارش : "+type+"&nbsp;"+ html+"</h4></div></div>"

            + table.outerHTML + "</body></html>";


        let newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        }, 3000)


    }
</script>