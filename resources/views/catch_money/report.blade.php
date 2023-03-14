<link rel="stylesheet" type="text/css" href="/css/jquery-ui.min.css">
<div class="row">
    <h3 style="text-align: center">گزارشات انتقال پول</h3>

    <form id="form-report" action="{{route('catch_money.report_data')}}" >
        <div class="col col-md-12">

            <div class="col col-md-3">
                <div class="form-group">
                    <label for="store">نوع نوع حساب:*</label>
                    <select name="store" id="store" class="form-control select2_1">
                        @foreach($money_stores as $money_store)
                            <option value="{{$money_store->store_id}}">{{$money_store->name}}</option>
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
            <div class="col col-md-6">

                @include('catch_money.month')
                @include('catch_money.year')
                @include('catch_money.bettwen_date')

            </div>




        </div>

    </form>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="report_section" style="margin-right: 30px; margin-bottom: 15px;">
                <button  type="submit" class="btn btn-primary" id="report">گرفتن گزارش</button>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead class="sum">
            <tr>
                <th>مقدار پول</th>
                <th>تاریخ</th>
                <th>توضیحات</th>
            </tr>
            </thead>


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

            var data = $("#form-report").serialize();
            var url = $("#form-report").attr('action');


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
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {

                    var trHTML = '';

                    $.each(response.data, function (i, item) {
                        trHTML += '<tr><td>' + item.amount + '</td><td>' + item.date + '</td><td>' + item.description + '</td>'
                        '</tr>';

                    });

                    $('#table_report').html(trHTML);
                    $(".total").text(response.store);


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
                $('#year').hide();
                $('#as_date').show();
                $('#between_date').show();
            } else {
                $('#selection').hide();
            }
        });
    });




</script>

