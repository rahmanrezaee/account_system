<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm"   action="{{ isset($first_equipment_money)? route('first_equipment_money.update',[$first_equipment_money->	first_money_eq_id]): route('first_equipment_money.create') }}">
        {{csrf_field()}}

        <div class="form-group required" id="form-equipment_name-error">
            <label for="equipment_name"> اسم وسیله*</label>
            <input type="text" name="equipment_name" id="equipment_name" class="form-control required"
                   value="{{old('equipment_name',isset($first_equipment_money)? $first_equipment_money->equipment_name:'')}}" >
            <span id="equipment_name-error" class="help-block"></span>
        </div>

        <div class="form-group required" id="form-money_amount-error">
            <label for="money_amount"> مقدار پول*</label>
            <input type="number" name="money_amount" id="money_amount" class="form-control required"
                   value="{{old('money_amount',isset($first_equipment_money)? $first_equipment_money->money_amount:'')}}" >
            <span id="money_amount-error" class="help-block"></span>
        </div>

        <!--date -->
        <div class="form-group required" id="form-date-error">
            <label for="date" class="control-label ">تاریخ:*</label>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="تاریخ/روز/سال" id="jalali-datepicker" name="date" autocomplete="off" value="{{old('date',isset($first_equipment_money)?$first_equipment_money->date:'')}}" >
                <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
            </div>
            <!-- /.input-group -->
        </div>

        <div class="form-group required" id="form-description-error">
            <label for="description">توضیحات:*</label>
            <textarea type="text" class="form-control required" id="description" name="description"
                      value="" cols="4"
                      placeholder="توضیحات">{{ old('description',isset($first_equipment_money)?$first_equipment_money->description:'' )}}</textarea>
            <span id="description-error" class="help-block"></span>

        </div>

        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('first_equipment_money.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
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


    $(document).ready(function () {
        getModalId();
        datatable();

    })
</script>

