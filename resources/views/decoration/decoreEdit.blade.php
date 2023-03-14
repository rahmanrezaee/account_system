<div class="container">
    {{--  tab headers  --}}

    <div class="tabbable boxed parentTabs">

        <div class="container">
            <h3>
                {{isset($panel_title) ?$panel_title :'ثبت دیکوریشن'}}
            </h3>

            <div class="row">
                <form method="post" id="frm"
                      action="{{ route('reserveDecoration.edit',$Customer_decor->res_decor_id) }}">

                    {{csrf_field()}}
                    <div class="row">

                        <div class="col-sm-12 col-md-3">
                            <div class="form-group required" id="form-customer_name-error">
                                <label for="customer_name" class="control-label">انتخاب مشتری</label>
                                <select name="customer_name" id="set_hall_name"
                                        class="form-control required">
                                    @foreach($customer as $cus)


                                        <option value="{{$cus->customer_id}}"
                                                @if($Customer_decor->customer_id == $cus->customer_id)
                                                        selected
                                                @endif >
                                            {{$cus->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="customer_name-error" class="help-block"></span>


                            </div>
                            {{-- select hall --}}

                        </div>
                        <div class="col-sm-12 col-md-3">
                            {{-- select customer --}}
                            <div class="form-group required" id="form-Hall_name-error">
                                <label for="Hall_name" style="width: 100%" class="control-label">انتخاب
                                    سالون</label>
                                <select name="Hall_name"  readonly="readonly" style="width: 100%;" id="Hall_name"
                                        class="form-control required" >

                                </select>
                                <span id="Hall_name-error" class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            {{-- select wedding date --}}
                            <div class="form-group required" id="form-register_date-error"
                                 style="padding-right: 0px;">
                                <label for="register_date" class="control-label">تاریخ روز</label>
                                <input id="jalali-datepicker" value="{{ $Customer_decor->register_date }}" placeholder="روز/ماه/سال" type=""
                                       class="form-control jalali-datepicker required" name="register_date"
                                >
                                <span id="register_date-error" class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12 col-md-3">
                            {{-- the weeding times --}}
                            <div class="form-group required" id="form-reserve_time-error" style="padding-left:0px;">
                                <label for="reserve_time" class="control-label">تاریخ محفل</label>
                                <input id="second-datepicker" placeholder="روز/ماه/سال" type=""
                                       class="form-control jalali-datepicker required" name="reserve_time"
                                       value="{{ $Customer_decor->date_reserve }}"
                                       >
                                <span id="reserve_time-error" class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group required " id="form-start_time-error">
                                <label for="start_time" class="control-label">زمان شروع :</label>
                                <input type="text" name="start_time" id="start_time"
                                       value="{{ $Customer_decor->time }}"
                                       class="form-control timepicker">
                                <span id="start_time-error" class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            {{-- select day or nigth --}}
                            <div class="form-group required" id="form-customer_name-error"
                                 style="padding-left:0px;">
                                <label for="day_nigth" class="control-label">روز یا شب</label>
                                <select name="day_nigth" id="day_nigth" class="form-control required">
                                    <option @if($Customer_decor->day_night == "شب" ) selected @endif value="شب">شب</option>
                                    <option @if($Customer_decor->day_night == "روز" ) selected @endif value="روز">روز</option>
                                </select>
                                <span id="day_nigth-error" class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12 col-md-3">
                            {{-- select Days of the Week --}}
                            <div class="form-group required" id="form-day_of_week-error"
                                 style="padding-right: 0px;">
                                <label for="day_of_week" class="control-label">ایام هفته</label>
                                <select name="day_of_week" id="day_of_week" class="form-control required">
                                    <option value="شنبه" @if($Customer_decor->week_day == "شنبه" ) selected @endif >شنبه
                                    </option>
                                    <option value="یک شنبه" @if($Customer_decor->week_day == "یک شنبه" ) selected @endif >یک
                                        شنبه
                                    </option>
                                    <option value="دو شنبه" @if($Customer_decor->week_day == "دو شنبه" ) selected @endif > دو
                                        شنبه
                                    </option>
                                    <option value="سه شنبه" @if($Customer_decor->week_day == "سه شنبه" ) selected @endif >سه
                                        شنبه
                                    </option>
                                    <option value="چهار شنبه" @if($Customer_decor->week_day == "چهار شنبه" ) selected @endif >
                                        چهار شنبه
                                    </option>
                                    <option value="پنج شنبه" @if($Customer_decor->week_day == "پنج شنبه" ) selected @endif >
                                        پنج شنبه
                                    </option>
                                    <option value="جمعه" @if($Customer_decor->week_day == "جمعه" ) selected @endif >جمعه
                                    </option>
                                </select>
                                <span id="day_of_week-error" class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            {{--Current payment --}}
                            <div class="form-group required" id="form-current_payment-error"
                                 style="padding-right: 0px;">
                                <label for="current_payment" class="control-label">پرداخت فعلی</label>
                                <input type="number" id="current_payment" class="form-control required "
                                       name="current_payment"  value="{{ $Customer_decor->current_payment }}" autofocus>
                                <span id="current_payment-error" class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-3">
                            {{--total payment --}}
                            <div class="form-group required" id="form-total_payment-error"
                                 style="padding-right: 0px;">
                                <label for="total_payment" class="control-label">مجموع</label>
                                <input type="number" id="total_payment" class="form-control required "
                                       name="total_payment"  value="{{ $Customer_decor->total_payment }}" autofocus>
                                <span id="total_payment-error" class="help-block"></span>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group required " id="form-discription-error">
                                <label for="discription" class="control-label">توضیخات :</label>
                                <textarea class="form-control" name="discription" id="discription"
                                          rows="1">
                                    {{
                                    $Customer_decor->description

                                    }}

                                </textarea>
                                <span id="discription-error" class="help-block"></span>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-3" style="margin-top:32px;">
                            <div class="form-group" style="margin-bottom: 10%;">
                                <div class="register">
                                    <a href="javascript:ajaxLoad('{{ route("decoration.list") }}')" class="btn btn-danger glyphicon glyphicon-backward"
                                       data-dismiss="modal">لغو</a>
                                    <button type="submit" id="btn_save"
                                            class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>

        {{--  second  --}}
    </div>
</div>


<script type="text/javascript">






    $(document).ready(function () {


        function setHall(selector){

            var c_id = $(selector).val();
            $.ajax({

                url: "decoration/reserve-halls-customer",
                type: "get",
                data: {"customer_id": c_id },
                success: function (data) {


                    console.log(data);
                    if (data != ""){
                        $("#Hall_name").html(data);

                    }else {
                        $("#Hall_name").html("");

                    }

                },
                error: function (errer) {
                    $("#Hall_name").html("");
                }

            });

        }

        setHall('#set_hall_name');

        // Tab 2 Find Hall
        $("#set_hall_name").change(function () {
            setHall(this);
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