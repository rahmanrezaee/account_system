<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
<h3 style="text-align: center">{{isset($panel_title) ?$panel_title :''}}</h3>
<div class="row">
    <form action="{{ route("customer.customerReport") }}" id="report">
        <div class="col col-md-12 d-flex">

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="type">مشتری</label>
                    <select name="reason" id="reason" class="form-control select2_1">
                        <option value="-1">مشتری تان را انتخاب کنید</option>
                        <option value="all">همه مشتری یان</option>
                        @foreach($customers as $customer)
                            <option value="{{$customer->customer_id}}">{{$customer->name}}</option>
                        @endforeach
                    </select>
                </div>


            </div>
            <div class="col col-md-3">
                <div class="form-group" id="choose">
                    <label for="type">نحوه گزارش:</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">حالت را انتخاب کنید</option>
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

            <div class="col col-md-2 d-flex align-items-center">
                <button id="get-report" type="button" class="btn btn-primary btn-rounded btn-sm"> گرفتن گزارش&nbsp;<i class="report_icon"></i></button>
            </div>


        </div>

    </form>
    <div class="col-md-12">
        <div class="col col-md-3">
            <div class="form-group">
                <select class="list-page form-control" >
                    <option>select count</option>
                    <option>5</option>
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
            </div>
        </div>
        <div class="col col-md-7"></div>
        <div class="col col-md-2 d-flex align-items-center">
            <button  id="print_report" class="btn btn-info btn-rounded btn-sm" onclick="imprimir()"> پرنت&nbsp;<i class="fa fa-print"></i></button>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-2">
            <p>مجموع پرداختی</p>
        </div>
        <div class="col-md-2">
            <p id="total_receiption">0</p>
        </div>
        <div class="col-md-2">
            مجموع باقیات
        </div>
        <div class="col-md-2">
            <p id="total_remaining">0</p>
        </div>
        <div class="col-md-2">
            <p >مجموع کل</p>
        </div>
        <div class="col-md-2">
            <p id="total">0</p>
        </div>
    </div>
    <div class="col-md-12">
        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead>
            <tr>
                <th>شماره</th>
                <th>کود فاکتوری فروخته شده</th>
                <th>تاریخ</th>
                <th>قیمیت دریافت شده </th>
                <th>باقیمانده پول</th>
                <th>مجموعه کل </th>
            </tr>
            </thead>

            <tbody id="reports">


            </tbody>
        </table>

        <div class="pagination" style="float: left" id="pagination">

        </div>
    </div>


</div>
<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {

        //
        $('#get-report').on('click', function (e) {
            e.preventDefault();
            var data = $("#report").serialize();
            var url = $("#report").attr('action');

            var html = '';
            var stock = $("#reason option:selected").text();

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

            // var Post = $(this).attr('method');
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    var data = response['finalData'].data;
                    var table = "";

                    if (data.length > 0 )
                    {
                        for(var i = 0; i<data.length ; i++){
                            table += '<tr>'+
                                '<td>' +data[i].sale_factor_id+'</td>'+
                                '<td>' +data[i].sale_factor_code+'</td>'+
                                '<td>' +data[i].sale_date+ '</td>'+
                                '<td>' +data[i].recieption_price+ '</td>'+
                                '<td>' +(data[i].total_price - data[i].recieption_price)+ '</td>'+
                                '<td>' +data[i].total_price+ '</td>'+
                                '</tr>';
                        }
                    } else {
                        table += '<tr><td colspan="6" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                    }
                    $(".report_icon").removeClass('fa fa-spinner fa-spin');
                    $('.loading').hide();

                    $('#reports').html(table);
                    $('#pagination').html(response['pagination']);

                    $("#total").html(response.total);
                    $("#total_receiption").html(response.total_recieption);
                    var remind = response.total - response.total_recieption;
                    $("#total_remaining").html(remind);

                }
            });
        });

    });


    // Pagination According to Selecting Number
    $(".list-page").change(function () {
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

        $('.loading').show();

        // var Post = $(this).attr('method');
        $.ajax({
            type: 'GET',
            url: url+ "/"+$(this).val(),
            data: data,
            dataType: 'json',
            success: function (response) {

                var data = response['data'].data;
                var table = "";

                if (data.length > 0 )
                {
                    for(var i = 0; i<data.length ; i++){
                        table += '<tr>'+
                            '<td>' +data[i].sale_factor_id+'</td>'+
                            '<td>' +data[i].sale_factor_code+'</td>'+
                            '<td>' +data[i].sale_date+ '</td>'+
                            '<td>' +data[i].recieption_price+ '</td>'+
                            '<td>' +(data[i].total_price - data[i].recieption_price)+ '</td>'+
                            '<td>' +data[i].total_price+ '</td>'+
                            '</tr>';
                    }
                } else {
                    table += '<tr><td colspan="6" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                }
                $('.loading').hide();

                $('#reports').html(table);
                $('#pagination').html(response['pagination']);

                $("#total").html(response.total);
                $("#total_receiption").html(response.total_recieption);
                var remind = response.total - response.total_recieption;
                $("#total_remaining").html(remind);
            }
        })

    })

    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });
        $('.select2_1').select2();

    });

    $(function () {
        $('#as_date').hide();
        $('#year').hide();
        $('#month').hide();
        $('#between_date').hide();
        $('#end_date').hide();
        $('#type').change(function () {

            if ($('#type').val() == 'month') {

                $('#between_date')
                $('#year').hide();
                $('#as_date').hide();
                $('#end_date').hide();
                $('#month').show();

            } else if ($('#type').val() == 'week') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#end_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'day') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#end_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'year') {
                $('#month').hide();
                $('#as_date').hide();
                $('#end_date').hide();
                $('#year').show();

            } else if ($('#type').val() == 'bt_date') {
                $('#month').hide();
                $('#as_date').show();
                $('#year').show();
            } else {
                $('#selection').hide();
            }
        });
    });

    // Pagination
    $(document).on('click','.pagination a', function (event) {
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

                for(var i = 0; i<data.length ; i++){
                    table += '<tr>'+
                        '<td>' +data[i].sale_factor_id+'</td>'+
                        '<td>' +data[i].sale_factor_code+'</td>'+
                        '<td>' +data[i].sale_date+ '</td>'+
                        '<td>' +data[i].recieption_price+ '</td>'+
                        '<td>' +(data[i].total_price - data[i].recieption_price)+ '</td>'+
                        '<td>' +data[i].total_price+ '</td>'+
                        '</tr>';
                }
                $('.loading').hide();
                $('#reports').html(table);
                $('#pagination').html(response['pagination']);

                $("#total").html(response.total);
                $("#total_receiption").html(response.total_recieption);
                var remind = response.total - response.total_recieption;
                $("#total_remaining").html(remind);
            }
        });
    })

</script>

<script type="text/javascript">

    function imprimir() {
    var customer = $("#reason option:selected").text();

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
    "<style> th{text-align:right !important} body{font-family:sahle}</style>"+
    "</head><body style='direction: rtl;font-family:sahel'>"+ "<h1 class='text-center'> گزارش مشتریان : "+customer+" </h1>"
        +
        "</h4></div><div class='col-md-2'><h4>تاریخ گزارش : "+type+"&nbsp;"+ html+"</h4></div></div>"
        + table.outerHTML +
        "</body></html>";

    newWin = window.open();
    newWin.document.write(d);
    newWin.setTimeout(function () {

    newWin.print();
    newWin.close();
    },3000)

    }
</script>

