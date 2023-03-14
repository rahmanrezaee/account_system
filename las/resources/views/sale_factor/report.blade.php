<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
<div class="row">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :''}}
    </h4>
    <form action="{{route('sale_factor.report_data')}}" id="report">
        <div class="col d-flex col-md-12">

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="stack_name">انتخاب گدام:*</label>
                    <select name="stack_name" id="stack_name" class="form-control select2_1">
                        <option value="all">همه گدام ها</option>
                        @foreach($stores as $store)

                            <option value="{{$store->store_id}}">{{$store->store_name}}</option>
                        @endforeach
                    </select>
                </div>


            </div>
            <div class="col col-md-3">
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
                @include('sale_factor.bettwen_date')

            </div>

            <div class="col col-md-2">
                <div class="form-group " id="between_date">
                    <label for="start_date" class="control-label ">از تاریخ:</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control year-end " placeholder="روز/ماه/سال" name="end-year" >


                </div>
            </div>
            <div class="col col-md-2 d-flex align-items-center">
                <button id="get-report" type="button" class="btn btn-info btn-rounded btn-sm"> گرفتن گزارش&nbsp;<i class="report_icon"></i></button>
            </div>

        </div>
    </form>

    <div class="col-md-12">
        <div class="col-md-2">
            <div class="form-group" >
                <select class="list-page form-control" >
                    <option>select count</option>
                    <option>5</option>
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
            </div>

        </div>
        <div class="form-group col-md-1  text-left" >
            <button class="btn btn-primary form-control" onclick="imprimir()"> پرنت <i class="fa fa-print"></i></button>
        </div>
    </div>
    <div class="col-md-12">


        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead>
            <tr>
                <th>آدی فکتور</th>
                <th>کودنمبر فکتور</th>
                <th> مقدار دریافت شده</th>
                <th>کل مقدار</th>
                <th>تاریخ خروج یا فروش</th>


            </tr>
            </thead>
            <tfoot id="footer_table">


            </tfoot>
            <tbody id="table_report">


            </tbody>
        </table>
        <div class="pagination" style="float: left" id="pagination">

        </div>
    </div>
</div>
<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">


    $(document).ready(function () {

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
                    // console.log(response['table_data'].data);
                    var data = response['table_data'].data;
                    var table = "";

                    if (data.length > 0)
                    {
                        for(var i = 0; i<data.length ; i++){
                            table += '<tr>'+
                                '<td>' +data[i].sale_factor_id+'</td>'+
                                '<td>' +data[i].sale_factor_code+'</td>'+
                                '<td>' +data[i].recieption_price+ '</td>'+
                                '<td>' +data[i].total_price+ '</td>'+
                                '<td>' +data[i].sale_date+ '</td>'+
                                '</tr>';
                        }

                        var tr = '<tr>';
                        tr += '<th colspan="2">مجموع محصولات ثبت شده</th>';
                        tr += '<th colspan="3">' + response.sum + '</th>';
                        tr += '</tr>';

                    } else {

                        table += '<tr><td colspan="5" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';

                    }
                    $(".report_icon").removeClass('fa fa-spinner fa-spin');
                    $('.loading').hide();

                    $('#table_report').html(table);
                    $('#footer_table').html(tr);
                    $('#pagination').html(response['pagination']);
                    // table.column(4).visible(false);
                    $(".dt-button").addClass("btn");

                }
            });
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
                // console.log(response['table_data'].data);
                var data = response['table_data'].data;
                var table = "";

                for(var i = 0; i<data.length ; i++){
                    table += '<tr>'+
                        '<td>' +data[i].sale_factor_id+'</td>'+
                        '<td>' +data[i].sale_factor_code+'</td>'+
                        '<td>' +data[i].recieption_price+ '</td>'+
                        '<td>' +data[i].total_price+ '</td>'+
                        '<td>' +data[i].sale_date+ '</td>'+
                        '</tr>';
                }
                $('.loading').hide();
                $('#table_report').html(table);

                var tr = '<tr>';
                tr += '<th colspan="2">مجموع محصولات ثبت شده</th>';
                tr += '<th colspan="3">' + response.sum + '</th>';
                tr += '</tr>';

                $('#footer_table').html(tr);
                $('#pagination').html(response['pagination']);
                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");

            }
        });
    })

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var data = $("#report").serialize();
        var url = $("#report").attr('action');

        var html = '';

        var type = $("#type option:selected").text();

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
        $('.loading').show();
        // var Post = $(this).attr('method');
        $.ajax({
            type: 'GET',
            url: url+"/"+$(this).val(),
            data: data,
            dataType: 'json',
            success: function (response) {
                // console.log(response['table_data'].data);
                var data = response['table_data'].data;
                var table = "";

                if (data.length > 0)
                {
                    for(var i = 0; i<data.length ; i++){
                        table += '<tr>'+
                            '<td>' +data[i].sale_factor_id+'</td>'+
                            '<td>' +data[i].sale_factor_code+'</td>'+
                            '<td>' +data[i].recieption_price+ '</td>'+
                            '<td>' +data[i].total_price+ '</td>'+
                            '<td>' +data[i].sale_date+ '</td>'+
                            '</tr>';
                    }

                    var tr = '<tr>';
                    tr += '<th colspan="2">مجموع محصولات ثبت شده</th>';
                    tr += '<th colspan="3">' + response.sum + '</th>';
                    tr += '</tr>';
                } else {
                    table += '<tr><td colspan="5" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';
                }
                $('.loading').hide();

                $('#table_report').html(table);
                $('#footer_table').html(tr);
                $('#pagination').html(response['pagination']);
                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");

            }
        });

    })

    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });

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

</script>

<script type="text/javascript">
    function imprimir() {

        var stack = $("#stack_name option:selected").text();


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
            "</head><body style='direction: rtl;font-family:sahel'>"+
            "<h1 class='text-center'>گزارش فاکتور های فروش : "+stack+" </h1>"
            +
            "</h4></div><div class='col-md-2'><h4>تاریخ گزارش : "+type+"&nbsp;"+ html+"</h4></div></div>"

            + table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        },3000)



    }
</script>
