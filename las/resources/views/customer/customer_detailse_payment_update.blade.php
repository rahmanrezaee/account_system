<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">

    <form method="post" id="frm"
          action="{{isset($detailsPayment) ?route('customer.details_payment_update',$detailsPayment->reserve_payment_id):route('customer.customer_payment')}}">
        {{isset($detailsPayment) ?method_field('put') :''}}
        {{csrf_field()}}


        <input type="hidden" id="reserve_payment_id" name="reserve_payment_id">

        <div class="form-group required" id="form-payment_amount-error">
            <label for="payment_amount"> :پرداخت فعلی*</label>
            <input type="number" name="payment_amount" id="payment_amount" class="form-control required"
                   value="{{old('current_payment',isset($detailsPayment)?$detailsPayment->payment_amount:'')}}" >
            <span id="current_payment-error" class="help-block"></span>
        </div>

        <div class="form-group  required" id="form-payment_date-error">
            <label for="payment_date" class="control-label ">تاریخ پرداخت:*</label>
            <div class="input-group">
                <input type="text" class="form-control required" placeholder="روز/ماه/سال"
                       id="jalali-datepicker" name="payment_date"
                       value="{{old('payment_date',isset($detailsPayment)?$detailsPayment->date:'')}}">
                <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
            </div>
            <span id="payment_date-error" class="help-block"></span>
        </div>

        <div class="modal-footer">
            <a href="javascript:ajaxLoad('{{route('customer_show_payment')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                اطلاعات
            </button>
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

    });

</script>