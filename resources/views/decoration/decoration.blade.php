<div class="container">
    {{--  tab headers  --}}

    <div class="tabbable boxed parentTabs">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#set1">انتخاب دیکوریشن</a></li>
            <li><a href="#set2">انتخاب سالون و تعین زمان محفل</a></li>
        </ul>
        {{--  start tab content  --}}
        <div class="tab-content">
            <div class="tab-pane active fade in" id="set1">
                <h3>
                    {{isset($panel_title) ?$panel_title :'ثبت دیکوریشن'}}
                </h3>

                <div class="row">
                    <form method="post" id="frm"
                          action="{{isset($cr)?route('decoration.crEdit',$cr->cus_res_decor_id):route('decoration.createCustomerReservation')}}">
                        {{isset($cr) ?method_field('put') :''}}
                        {{csrf_field()}}
                        <div class="row">

                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="form-group required" id="form-customer_name-error">
                                    <label for="customer_name" class="control-label">انتخاب مشتری</label>
                                    <select name="customer_name" id="set_hall_name"
                                            class="form-control required">
                                        <option value="">
                                            مشتری را انتخاب کنید..
                                        </option>
                                        @foreach($customer as $cus)
                                            <option value="{{$cus->customer_id}}">
                                                {{$cus->name . $cus->phone}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="customer_name-error" class="help-block"></span>


                                </div>
                                {{-- select hall --}}

                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                {{-- select customer --}}
                                <div class="form-group required" id="form-Hall_name-error">
                                    <label for="Hall_name" style="width: 100%" class="control-label">انتخاب
                                        سالون</label>
                                    <select name="Hall_name" readonly="readonly" style="width: 100%;" id="Hall_name"
                                            class="form-control required">

                                    </select>
                                    <span id="Hall_name-error" class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                {{-- select wedding date --}}
                                <div class="form-group required" id="form-register_date-error"
                                    >
                                    <label for="register_date" class="control-label">تاریخ روز</label>
                                    <input id="jalali-datepicker" autocomplete="off" placeholder="روز/ماه/سال" type=""
                                           class="form-control jalali-datepicker required" name="register_date" value=""
                                           autofocus>
                                    <span id="register_date-error" class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-12 col-md-4 col-lg-43">
                                {{-- the weeding times --}}
                                <div class="form-group required" id="form-reserve_time-error" style="padding-left:0px;">
                                    <label for="reserve_time" class="control-label">تاریخ محفل</label>
                                    <input id="second-datepicker" autocomplete="off" placeholder="روز/ماه/سال" type=""
                                           class="form-control jalali-datepicker required" name="reserve_time" value=""
                                           autofocus>
                                    <span id="reserve_time-error" class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="form-group required " id="form-start_time-error">
                                    <label for="start_time" autocomplete="off" class="control-label">زمان شروع :</label>
                                    <input type="text" name="start_time" id="start_time"
                                           class="form-control timepicker">
                                    <span id="start_time-error" class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                {{-- select day or nigth --}}
                                <div class="form-group required" id="form-customer_name-error"
                                >
                                    <label for="day_nigth"  class="control-label">روز یا شب</label>
                                    <select name="day_nigth" id="day_nigth" class="form-control required">
                                        <option value="شب">شب</option>
                                        <option value="روز">روز</option>
                                    </select>
                                    <span id="day_nigth-error" class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-12 col-md-4 col-lg-4">
                                {{-- select Days of the Week --}}
                                <div class="form-group required" id="form-day_of_week-error"
                                >
                                    <label for="day_of_week" class="control-label">ایام هفته</label>
                                    <select name="day_of_week" id="day_of_week" class="form-control required">
                                        <option value="شنبه">شنبه</option>
                                        <option value="یکشنبه">یکشنبه</option>
                                        <option value="دو شنبه">دو شنبه</option>
                                        <option value="سه شنبه">سه شنبه</option>
                                        <option value="چهار شنبه">چهار شنبه</option>
                                        <option value="پنج شنبه">پنج شنبه</option>
                                        <option value="جمعه">جمعه</option>
                                    </select>
                                    <span id="day_of_week-error" class="help-block"></span>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                {{--Current payment --}}
                                <div class="form-group required" id="form-current_payment-error"
                                >
                                    <label for="current_payment" autocomplete="off"  class="control-label">پرداخت فعلی</label>
                                    <input type="number" id="current_payment" class="form-control required "
                                           name="current_payment" value="" autofocus>
                                    <span id="current_payment-error" class="help-block"></span>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4 col-lg-4">
                                {{--total payment --}}
                                <div class="form-group required" id="form-total_payment-error"
                                >
                                    <label for="total_payment" autocomplete="off"  class="control-label">مجموع</label>
                                    <input type="number" id="total_payment" class="form-control required "
                                           name="total_payment" value="" autofocus>
                                    <span id="total_payment-error" class="help-block"></span>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-4">
                                <div class="form-group required " id="form-discription-error">
                                    <label for="discription" class="control-label">توضیخات :</label>
                                    <textarea class="form-control" name="discription" id="discription"
                                              rows="1"></textarea>
                                    <span id="discription-error" class="help-block"></span>
                                </div>
                            </div>


                            <div class="col-sm-12 col-md-6 col-lg-6" style="margin-top: 8%">
                                <div class="form-group">
                                    <div class="register">
                                        <button type="submit" id="btn_save"
                                                class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره
                                        </button>
                                        <a href="" class="btn btn-danger glyphicon glyphicon-backward"
                                           data-dismiss="modal">لغو</a>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
            {{--  second  --}}
            <div class="tab-pane fade" id="set2">

                <form method="post" id="frm" action="{{ route('decoration.reserveDecor') }}">

                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group required " id="form-chose_customer-error">
                                <label for="chose_customer" class="control-label">انتخاب مشتری :</label>
                                <select name='customer_id' style="width: 100%" id='chose_customer'
                                        class="form-control select2_1">
                                    <option value=""></option>
                                    @foreach($customer as $cus)
                                        <option value="{{ $cus->customer_id }}">{{$cus->name . $cus->phone}}</option>
                                    @endforeach
                                </select>
                                <span id="chose_customer-error" class="help-block"></span>
                            </div>
                        </div>
                        <div style="display: none" class="col-sm-12 col-md-4 col-lg-4" id="hall_list">


                            <div class="form-group required " id="form-decoration_type-error">
                                <label for="decoration_type" class="control-label">انتخاب سالون :</label>
                                <select name='hall_id' id='selected-hall' class="form-control select-ajax">

                                </select>
                                <span id="decoration_type-error" class="help-block"></span>
                            </div>

                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group required " id="form-decoration_type-error">
                                <label for="decoration_type" class="control-label">انتخاب نوع دیکوریشن :</label>
                                <select name='decoration_type' id='decoration_type' class="form-control select-ajax">
                                    <option value=""></option>
                                    @foreach($decor_type as $d)
                                        <option value="{{ $d->type_decor_name }}">{{$d->type_decor_name}}</option>
                                    @endforeach
                                </select>
                                <span id="decoration_type-error" class="help-block"></span>
                            </div>
                        </div>
                        <input type="hidden" name="res_decor_id" id="res_decor_id" value="">

                    </div>
                    <div id="test">

                        <table id="foot_table" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                            <tr>
                                <th>شماره</th>
                                <th>نوع دیکوریشن</th>
                                <th>نام دیکوریشن</th>
                                <th>انتخاب دیکوریشن</th>

                            </tr>
                            </thead>


                            <tbody id="foots-body-table">


                            </tbody>
                        </table>

                        <div class="form-group" style="margin-bottom: 10%;">
                            <div class="col-md-6 col-md-offset-4 register">
                                <button type="submit" id="btn_save"
                                        class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره
                                </button>
                                <a href="" class="btn btn-danger glyphicon glyphicon-backward"
                                   data-dismiss="modal">لغو</a>

                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
        {{--  end tab content  --}}
    </div>

</div>

<script type="text/javascript">


    function ajaxMethod(url, method, para, id) {

        var output = "";
        $.ajax({
            url: url,
            type: method,
            data: para,
            success: function (data) {

                $("#" + id).html("<td>" + data + "</td>");


            },
            error: function (errer) {

            }

        });

        return output;
    }

    function loadDataChange(selecter, puter) {

        $(selecter).change(function () {

            $(puter).text($(this).val());
        })

    }

    function hall_data() {

        var data = $('#Hall_name').select2('data');

        var table = "";
        var set_hall = "";
        data.forEach(function (item) {
            table += "<tr><td>" + item.text + "</td></tr>"
            table += "<tr id='hall-" + item.id + "'></tr>"
            $("#hall_design").html(table);
            var c_id = $("#customer_name_hall_reserved").val();
            var par = {
                "custome_id": c_id,
                "hall_id": item.id
            }
            var s = ajaxMethod("decoration/show-hall-factor", "get", par, "hall-" + item.id)
            console.log(s)
            set_hall += item.text + " , ";

        })


        $(".hall-selected").text(set_hall);
    }


    $(document).ready(function () {

        // Tab 2 Find Hall



        $("#chose_customer").change(function () {

            $("#hall_list").fadeIn(2000);

            var id = $(this).val();
            $.ajax({

                url: "decoration/reserve-halls",
                type: "get",
                data: {"id_customer": id},
                success: function (data) {




                    $("#selected-hall").html(data);


                }, error: function () {

                }


            })


        });

        $("#selected-hall").change(function () {


            var id = $("#chose_customer").val();

            $.ajax({
                url: "decoration/reserve-decoration",
                type: "get",
                data: {"id_customer": id},
                success: function (data) {


                    $("#res_decor_id").val(data);


                }, error: function () {

                }


            })

        });


        loadDataChange("#jalali-datepicker", ".day-date")
        loadDataChange("#celebration_date", ".celebrate-date")

        loadDataChange("#start_time", ".start-time")

        loadDataChange("#day_nigh", "#second-datepicker")
        loadDataChange("#celebration_date", ".celebrate-date")
        loadDataChange("#number_of_guests", ".count-host")
        loadDataChange("#weak_days", ".day-week")

        loadDataChange("#current_payment", ".current-pay")
        loadDataChange("#total_money", ".total-pay")


        $("#set_hall_name").change(function () {


            var c_id = $(this).val();
            $.ajax({

                url: "decoration/reserve-halls-customer",
                type: "get",
                data: {"customer_id": c_id},
                success: function (data) {



                    $("#Hall_name").html(
                        '<option value="'+data[0].rh+'">'+data[0].reserveHallName+'</option>'
                    );
                    $("#jalali-datepicker").val(data[0].date_registration);
                    $("#second-datepicker").val(data[0].date_reservation);
                    $("#day_nigth").val(data[0].day_nigh);
                    $("#start_time").val(data[0].end_time);


                    var b = data[0].week_day;
                    $('#day_of_week').find('option').each(function(i,e){

                        if($(e).val() == b){

                            $(e).attr('selected', true);;
                        }
                    });
                },
                error: function (errer) {

                }

            });

        });

        $("#decoration_type").change(function () {

            var cust = $("#chose_customer").val();
            var decor_type = $("#decoration_type").val();


            $.ajax({
                type: "get",
                url: "decoration/filter/" + decor_type,
                contentType: false,
                success: function (data) {


                    $('#foots-body-table').html(data.table_data);

                    table = $('#foot_table').DataTable({

                        paging: true,
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                        destroy: true,
                        retrieve: true,
                    });


                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });

        });

        $("ul.nav-tabs a").click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        // Time Picker Initialization from celebration start time
        $('#start_time').timepicker();

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

        /*================
        EDTITABEL TABLE
        * ===============*/
        $('.select2_1').select2();


        //second date input
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

        kamaDatepicker('second-datepicker', opt);

        /*================
        EDTITABEL TABLE
        * ===============*/
        $('.select2_1').select2();

    });
</script>