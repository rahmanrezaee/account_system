<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
<div class="row">
    <h3 style="text-align: center">گذارش مصارف</h3>
    <form action="{{route('expense.report_data')}}" id="report">
        <div class="col d-flex col-md-12">

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="reason">نوع مصرف:*</label>
                    <select name="reason" id="reason" class="form-control select2_1">
                        <option value="all">همه مصارف</option>
                        @foreach($reason_pays as $reason_pay)
                            <option value="{{$reason_pay->expense_reason_id}}">{{$reason_pay->title}}</option>
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

                @include('sale_factor.endyear')
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
            <thead class="sum">
            <tr>
                <th>عنوان</th>
                <th>مقدار</th>
                <th>پول رایچ</th>
                <th> تاریخ پرداخت</th>
                <th>توضیحات</th>
            </tr>
            </thead>
            <tfoot style="text-align: right">
            <tr>
                <td>مجموعه</td>
                <td class="total" colspan="3">0</td>
                <td></td>
            </tr>
            </tfoot>

            <tbody id="content-display">


            </tbody>
        </table>
        <div class="pagination" style="float: left" id="pagination">

        </div>
    </div>
</div>
<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {

        $(document).off("click");
        $('#get-report').on('click', function (e) {
            e.preventDefault();

            var data = $("#report").serialize();
            var url = $("#report").attr('action');


            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();

            // var Post = $(this).attr('method');
            var request1 = $.ajax({
                type: 'get',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    var data = response['table_data'].data;
                    var table = "";


                    if (data.length > 0)
                    {
                        for(var i = 0; i<data.length ; i++){
                            table += '<tr>'+
                                '<td>' +data[i].title+'</td>'+
                                '<td>' +data[i].amount+ '</td>'+
                                '<td>' +data[i].currency_name+ '</td>'+
                                '<td>' +data[i].pay_date+ '</td>'+
                                '<td>' +data[i].description+ '</td>'+
                                '</tr>';
                        }
                    } else {
                        table += '<tr><td colspan="5" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                    }
                    $(".report_icon").removeClass('fa fa-spinner fa-spin');
                    $('.loading').hide();

                    $('#content-display').html(table);
                    $('#pagination').html(response['pagination']);

                    $(".total").text(response.sum)

                    $(".dt-button").addClass("btn");


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
    $(document).on('click','.pagination a', function (event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var url = $(this).attr("href");

        var data = $("#report").serialize();

        $('.loading').show();

        $.ajax({
            type: 'get',
            url: url,
            data: data,
            dataType: 'json',
            success: function (response) {
                var data = response['table_data'].data;
                var table = "";

                for(var i = 0; i<data.length ; i++){
                    table += '<tr>'+
                        '<td>' +data[i].title+'</td>'+
                        '<td>' +data[i].amount+ '</td>'+
                        '<td>' +data[i].first_name+'&nbsp;'+data[i].last_name+ '</td>'+
                        '<td>' +data[i].currency+ '</td>'+
                        '<td>' +data[i].pay_date+ '</td>'+
                        '<td>' +data[i].description+ '</td>'+
                        '</tr>';
                }
                $('.loading').hide();
                $('#content-display').html(table);
                $('#pagination').html(response['pagination']);

                $(".total").text(response.sum)

                $(".dt-button").addClass("btn");

            }
        });
    })

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var data = $("#report").serialize();
        var url = $("#report").attr('action');

        $('.loading').show();
        var request1 = $.ajax({
            type: 'get',
            url: url+"/"+$(this).val(),
            data: data,
            dataType: 'json',
            success: function (response) {
                var data = response['table_data'].data;
                var table = "";

                if (data.length > 0)
                {
                    for(var i = 0; i<data.length ; i++){
                        table += '<tr>'+
                            '<td>' +data[i].title+'</td>'+
                            '<td>' +data[i].amount+ '</td>'+
                            '<td>' +data[i].first_name+'&nbsp;'+data[i].last_name+ '</td>'+
                            '<td>' +data[i].currency+ '</td>'+
                            '<td>' +data[i].pay_date+ '</td>'+
                            '<td>' +data[i].description+ '</td>'+
                            '</tr>';
                    }
                } else {
                    table += '<tr><td colspan="3" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                }
                $('.loading').hide();

                $('#content-display').html(table);
                $('#pagination').html(response['pagination']);

                $(".total").text(response.sum)

                $(".dt-button").addClass("btn");

            }
        });

    })

</script>

<script type="text/javascript">
    function imprimir() {

        var expense_type = $("#reason option:selected").text();

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
            "</head><body style='direction: rtl;font-family:sahel'>"+ "<h1 class='text-center'> گزارش مصارف : "+expense_type+" </h1>"
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