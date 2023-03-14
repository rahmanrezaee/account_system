<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
<div class="row">
    <h3 style="text-align: center">گذارش مصارف</h3>
    <form action="{{route('expense.report_data')}}" id="report">
        <div class="col col-md-12">

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="reason">نوع مصرف:*</label>
                    <select name="reason" id="reason" class="form-control select2_1">
                        <option value="all">همه مصارف</option>
                        @foreach($reason_pays as $reason_pay)

                            <option value="{{$reason_pay->id}}">{{$reason_pay->title}}</option>
                        @endforeach
                    </select>
                </div>


            </div>
            <div class="col col-md-3">
                <div class="form-group" id="choose">
                    <label for="type">نحوه گزارش:</label>
                    <select id="type" name="type" class="form-control">
                        <option  value="type-1">نوع</option>
                        <option  value="day">روز</option>
                        <option value="week">هفته</option>
                        <option value="month">ماه</option>
                        <option value="year">سال</option>
                        <option value="bt_date">بین تاریخ</option>
                    </select>
                </div>



            </div>
            <div class="col col-md-3">

                @include('expense.month')
                @include('expense.year')
                @include('expense.bettwen_date')

            </div>
            <div class="col col-md-3">
                <div class="form-group " id="between_date">
                    <label for="end_date" class="control-label ">تاریخ:*</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="روز/ماه/سال" id="jalali-enddate"
                               name="end_date" >
                        <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>


        </div>

    </form>
    <div class="col-md-12">
        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead class="sum">
            <tr>
                <th>مقدار</th>
                <th>پول رایچ</th>
                <th>زمان</th>
            </tr>
            </thead>
            <tfoot style="text-align: right">
            <tr>
                <td >مجموعه</td>
                <td class="total">0</td>
                <td ></td>
            </tr>
            </tfoot>

            <tbody id="table_report">


            </tbody>
        </table>
    </div>
</div>
<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {

        $('#report').on('click', function (e) {
            e.preventDefault();

            var data = $(this).serialize();
            var url = $(this).attr('action');


            var html = '';
            var companyName = $("#reason option:selected").text();

            var type = $("#type option:selected").text();
            if (type != "نوع") {

                if (type == "روز") {

                    html += getdate();


                }
                if (type == "هفته") {

                    html += getdate("week")+" "+ getdate("month")+"/"+getdate("year");

                }
                if (type == "ماه") {

                    html += $("#month_r option:selected").text() +"/"+getdate("year");
                }
                if (type == "سال") {

                    let txt = $("#jalali-datepicker").val();
                    let currenyear =  txt.split("/")[0];
                    $("#jalali-datepicker").val(currenyear);
                    html += currenyear;



                }
                if (type == "بین تاریخ") {

                    html += $("#jalali-enddate").val() + " تا " + $("#jalali-startdate").val();

                }

            }



            // var Post = $(this).attr('method');
            var request1= $.ajax({
                type: 'GET',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    console.log(response.table_data);
                    // $('#buy_factor_report').html(data.table_data);

                    table = $('#example').DataTable({
                        destroy: true,
                        dom: 'Bfrtip',
                        data: response.table_data,
                        buttons: [
                            {
                                extend: 'print',
                                footer: true,
                                customize: function (win) {
                                    $(win.document.body)
                                        .css({"direction": "rtl"})
                                    $(win.document.body).find('h1').css("textAlign", "center");

                                    let titleSelect = $(win.document.body).find('h1');
                                    titleSelect.append("<br>");
                                    titleSelect.append("<div style='text-align: right;font-size: 18px'>" +
                                        "<span style='margin-right: 30px;margin-left: 10px'> مصارف : " + companyName + '</span>  ' +
                                        "<span style='margin-right: 30px;margin-left: 10px'> گزارش از: " + html + '</span>' +
                                        '</div>')

                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');

                                }

                                , title: '{!! isset($panel_title) ?$panel_title :'' !!}',


                            },
                            {
                                extend: 'excelHtml5',
                                autoFilter: true,
                                footer: true,
                                sheetName: 'Exported data'
                            }
                        ],

                        columns: [

                            {title: 'مقدار', data: 'amount'},
                            {title: 'پول رایچ', data: 'currency'},
                            {title: ' زمان', data: 'pay_date'},
                        ],


                    });


                    $(".total").text(response.sum)

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

        }

        kamaDatepicker('jalali-datepicker', opt);
        kamaDatepicker('jalali-enddate', opt);
        kamaDatepicker('jalali-startdate', opt);
        kamaDatepicker('tat', opt);

        /*================
          EDTITABEL TABLE
        * ===============*/
        $('.select2_1').select2();

    });

    $(document).ready(function() {

    } );

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

