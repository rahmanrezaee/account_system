<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm"   action="{{ isset($revenue)? route('revenue.update',[$revenue->revenue_id]): route('revenue.create') }}">
        {{csrf_field()}}

        <div class="form-group required" id="form-account_type-error">
            <label for="account_type">نوعیت حساب:*</label>
            <select name="account_type" id="account_type" class="form-control select2_1">
                @foreach($moneyStores as $money_store)
                    <option value="{{$money_store->store_id}}" @if (isset($revenue) && $money_store->store_id == $revenue->account_type)
                        selected
                    @endif>{{$money_store->name}}</option>
                @endforeach
            </select>
            <span id="account_type-error" class="help-block"></span>
        </div>


        <div class="form-group required" id="form-money_amount-error">
            <label for="money_amount"> مقدار پول*</label>
            <input type="number" name="money_amount" id="money_amount" class="form-control required"
                   value="{{old('money_amount',isset($revenue)? $revenue->money_amount:'')}}" >
            <span id="money_amount-error" class="help-block"></span>
        </div>


        <div class="form-group required" id="form-revenue_description-error">
            <label for="revenue_description">توضیحات:*</label>
            <textarea type="text" class="form-control required" id="revenue_description" name="revenue_description"
                      value="" cols="4"
                      placeholder="توضیحات">{{ isset($revenue)?$revenue->description:'' }}</textarea>
            <span id="revenue_description-error" class="help-block"></span>

        </div>

        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('revenue.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
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

