<h4 class="box-title ">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{ isset($car)? route('car.update',[$car->	car_revenue_id]):route('car.create') }}">
        {{csrf_field()}}

        <div class="form-group required" id="form-car_amount-error">
            <label for="car_amount"> مقدار درآمد*</label>
            <input type="number" name="car_amount" id="car_amount" class="form-control required"
                   value="{{old('car_amount',isset($car)?$car->revenue_amount:'')}}" >
            <span id="car_amount-error" class="help-block"></span>
        </div>

        <div class="form-group" id="form-car_date-error">
            <label for="car_date" class="control-label ">تاریخ :*</label>
            <div class="input-group">
                <input type="text" class="form-control required" placeholder="روز/ماه/سال"
                       id="jalali-datepicker" name="car_date"
                       value="{{old('car_date',isset($car)?$car->date:'') }}">
                <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
            </div>
            <span id="car_date-error" class="help-block"></span>
        </div>

        <div class="form-group required" id="form-car_description-error">
            <label for="car_description">توضیحات:*</label>
            <textarea type="text" class="form-control required" id="car_description" name="car_description"
                      value="" cols="4"
                      placeholder="توضیحات">{{ isset($car)?$car->description:'' }}</textarea>
            <span id="car_description-error" class="help-block"></span>

        </div>

        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('money_store.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        </div>
    </form>


</div>

<script type="text/javascript">

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

            buttonsColor: "پیش فرض ",

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

    });

</script>

