<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
<div class="row">
    <h3 style="text-align: center">گذارش فکتور های خارج شده ازگدام</h3>

    <form action="{{route('sale_factor.report_data')}}" id="report">
        <div class="col d-flex col-md-12">

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="reason">انتخاب گدام:*</label>
                    <select name="reason" id="reason" class="form-control select2_1">
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
                    <label for="end_date" class="control-label ">تاریخ:*</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="روز/ماه/سال" id="jalali-enddate"
                               name="end_date">
                        <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="col col-md-2 d-flex align-items-center">

                <input value="گرفتن گزازش" id="get-report" type="button" class="btn btn-info btn-rounded btn-sm">
            </div>


        </div>
    </form>


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

                    html += getdate();


                }
                if (type == "هفته") {

                    html += getdate("week") + " " + getdate("month") + "/" + getdate("year");

                }
                if (type == "ماه") {

                    html += $("#month_r option:selected").text() + "/" + getdate("year");
                }
                if (type == "سال") {

                    let txt = $("#jalali-datepicker").val();
                    let currenyear = txt.split("/")[0];
                    $("#jalali-datepicker").val(currenyear);
                    html += currenyear;


                }
                if (type == "بین تاریخ") {

                    html += $("#jalali-enddate").val() + " تا " + $("#jalali-startdate").val();

                }

            }
            // var Post = $(this).attr('method');
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    $('#table_report').html(data.table_data);


                    var tr = '<tr>';
                    tr += '<th colspan="2">مجموع محصولات ثبت شده</th>';
                    tr += '<th colspan="3">' + $.number(data.sum) + '</th>';
                    tr += '</tr>';

                    $('#footer_table').html(tr);

                    var table = $('#example').DataTable({
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
                                    titleSelect.append("<div style='text-align: right;font-size: 18px'>" +
                                        "<span style='margin-right: 30px;margin-left: 10px'> گزارش موجودی گدام:   : " + stock + '</span>  ' +
                                        "<span style='margin-right: 30px;margin-left: 10px'> تاریخ :   : " + html + '</span>  ' +
                                        '</div>')

                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');


                                },
                                title: '{!! isset($panel_title) ?$panel_title :'گزارشات موجودی گدام' !!}',

                                exportOptions: {
                                    columns: ':not(:last-child)',
                                }

                            },
                            {
                                {{--title: '{!! isset($panel_title) ?$panel_title :'گزارشات موجودی گدام' !!}',--}}

                                extend: 'excelHtml5',
                                autoFilter: true,
                                sheetName: 'Exported data'
                            }
                        ],

                    });

                    // table.column(4).visible(false);
                    $(".dt-button").addClass("btn");


                }
            });
        });

    });


    $(document).ready(function () {
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


</script>

