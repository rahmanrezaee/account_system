<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">

    <form method="post" id="frm"
          action="{{isset($totalPayment) ?route('customer.payment_update',$totalPayment->total_payment_id):route('customer.customer_payment')}}">
        {{isset($totalPayment) ?method_field('put') :''}}
        {{csrf_field()}}


        <input type="hidden" id="total_payment_id" name="total_payment_id">
        <div class="form-group required" id="form-total_payment-error">
            <label for="total_payment"> :مجموعه قابل پرداخت*</label>
            <input type="number" name="total_payment" id="total_payment" class="form-control required"
                   value="{{old('total_payment',isset($totalPayment)?$totalPayment->total_payment:'')}}">
            <span id="total_payment-error" class="help-block"></span>

        </div>

        <div class="form-group required" id="form-current_payment-error">
            <label for="current_payment"> :پرداخت فعلی*</label>
            <input type="number" name="current_payment" id="current_payment" class="form-control required"
                   value="{{old('current_payment',isset($totalPayment)?$totalPayment->current_payment:'')}}" >
            <span id="current_payment-error" class="help-block"></span>
        </div>

        <div class="form-group  required" id="form-payment_date-error">
            <label for="payment_date" class="control-label ">تاریخ پرداخت:*</label>
            <div class="input-group">
                <input type="text" class="form-control required" placeholder="روز/ماه/سال"
                       id="jalali-datepicker" name="payment_date"
                       value="{{old('payment_date',isset($totalPayment)?$totalPayment->date:'')}}">
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