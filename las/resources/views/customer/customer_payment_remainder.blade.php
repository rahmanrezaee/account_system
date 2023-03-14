<div class="col-lg-12 col-md-12 col-xs-12">
    <h3 style="text-align: center;">
        {{isset($panel_title) ?$panel_title :'تحویل باقیات مشتری'}}
    </h3>
    <div class="panel panel-info col-sm-12 col-md-4 col-lg-4" style="margin-top: 4%">


        @if(isset($hallInfo))
            <div class="panel-heading">
                <td ><h4>معلومات بدهی مشتری :</h4></td>
            </div>
            <tr>
                <td><h4> نام مشتری :{{$hallInfo->name}}</h4></td>
                <td><h4> ازبابت سالون {{isset($hallInfo)?  $hallRemainder:''}}</h4></td>
                <td><h4>ازبابت دیکور:{{isset($decorRemainder)?  $decorRemainder:''}}</h4></td>
                <td><h4>ازبابت موسیقی :{{isset($musicRemainder)?$musicRemainder:''}}</h4></td>
                <td><h4>ازبابت فلمبردار : {{isset($filmRemainder)?$filmRemainder:''}}</h4></td>
            </tr>

        @endif

    </div>
    <div class="col-lg-6 col-md-6 col-xs-12">
        <form method="post" id="frm"
              action="{{route('customer.customer_payment')}}">
            {{csrf_field()}}

            <input type="hidden" id="payment_id" name="payment_id"
                   value="{{isset($hallInfo)?$hallInfo->res_hall_id :''}}">
            <input type="hidden" id="customer_id" name="customer_id"
                   value="{{isset($hallInfo)?$hallInfo->customer_id :''}}">

            <div class="form-group required" id="form-customer_type-error">
                <label for="customer_type">نوعیت پرداخت مشتری</label>
                <select name="payment_type" id="payment_type" class="form-control ">
                    <option value="سالون"> سالون ها</option>
                    <option value="غذا"> مینوی غذا</option>
                    <option value="دیکوریشن"> دیکوریشن</option>
                    <option value="موسیقی">موسیقی</option>
                    <option value="فلمبردار"> فلمبردار</option>
                    <option value="متفرقه">متفرقه</option>
                </select>
                <span id="payment_type-error" class="help-block"></span>
            </div>

            <div class="form-group required" id="form-current_payment-error">
                <label for="current_payment"> :پرداخت فعلی*</label>
                <input type="number" name="current_payment" id="current_payment" class="form-control required">
                <span id="current_payment-error" class="help-block"></span>
            </div>

            <div class="input-group">
                <input type="text" class="form-control required" placeholder="روز/ماه/سال" id="jalali-datepicker"
                       name="payment_date" id="jalali-datepicker">
                <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
            </div>

            <div class="form-group required" id="form-description-error">
                <label for="description">توضیحات:*</label>
                <textarea type="text" class="form-control required" id="description" name="description" rows="4"
                          placeholder="توضیحات"> </textarea>
                <span id="description-error" class="help-block"></span>

            </div>

            <div class="form-group" style="margin-top: 2%">
                <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save"></i></button>
                <a href="javascript:ajaxLoad('{{route('customer_show_payment')}}')" class="btn btn-danger">لغو<i
                            class="fa fa-backward"></i></a>
            </div>
        </form>
    </div>
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