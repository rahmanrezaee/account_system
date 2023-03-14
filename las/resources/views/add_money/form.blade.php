<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{  isset($addMoney)? route('add_money.update',[$addMoney->add_money_id]) :route('add_money.create') }}">
        {{--{{isset($addMoney) ?method_field('put') :''}}--}}
        {{csrf_field()}}

        <div class="form-group required" id="form-add_money_amount-error">
            <label for="add_money_amount"> مقدار پول*</label>
            <input type="number" name="add_money_amount" id="add_money_amount" class="form-control required"
                   value="{{old('add_money',isset($addMoney)?$addMoney->money_amount:'')}}" >
            <span id="add_money_amount-error" class="help-block"></span>
        </div>

        <div class="form-group required" id="form-account_id-error">
            <label for="account_id">نوعیت حساب </label>
            <select name="account_id" id="account_id" class="form-control ">
                @foreach ($moneyStores as $moneyStore)
                    <option value="{{ $moneyStore->store_id }}" @if (isset($addMoney) && $moneyStore->store_id == $addMoney->account_type)
                        selected
                        @endif>{{ $moneyStore->name }}</option>
                @endforeach
            </select>
            <span id="account_id-error" class="help-block"></span>
        </div>

        <div class="form-group  required" id="form-add_money_date-error">
            <label for="add_money_date" class="control-label ">تاریخ برداشت:*</label>
            <div class="input-group">
                <input type="text" class="form-control required" placeholder="روز/ماه/سال"
                       id="jalali-datepicker" name="add_money_date"
                       value="{{ old('add_money_date',isset($addMoney)?$addMoney->date:'') }}">
                <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
            </div>
            <span id="add_money_date-error" class="help-block"></span>
        </div>

        <div class="form-group required" id="form-add_money_description-error">
            <label for="add_money_description">توضیحات:*</label>
            <textarea type="text" class="form-control required" id="add_money_description" name="add_money_description"
                      value="" cols="4"
                      placeholder="توضیحات">{{ isset($addMoney)?$addMoney->description:'' }}</textarea>
            <span id="add_money_description-error" class="help-block"></span>

        </div>

        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('add_money.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
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

