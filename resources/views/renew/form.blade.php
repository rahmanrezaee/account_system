<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm"   action="">
        {{csrf_field()}}

        <div class="form-group required" id="form-account_id-error">
            <label for="account_id">نوعیت حساب </label>
            <select name="account_id" id="sender_id" class="form-control required ">
            </select>
            <span id="account_id-error" class="help-block"></span>
        </div>


        <div class="form-group required" id="form-money-amount-error">
            <label for="money-amount"> مقدار پول*</label>
            <input type="number" name="money-amount" id="money-amount" class="form-control required"
                   value="{{old('money-amount')}}" >
            <span id="money-amount-error" class="help-block"></span>
        </div>


        <div class="form-group required" id="form-renew_description-error">
            <label for="renew_description">توضیحات:*</label>
            <textarea type="text" class="form-control required" id="renew_description" name="renew_description"
                      value="" cols="4"
                      placeholder="توضیحات"></textarea>
            <span id="renew_description-error" class="help-block"></span>

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

