<h3 class="box-title text-center">
    {{isset($panel_title) ?$panel_title :''}}
</h3>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{ route('options.general') }}">
        {{csrf_field()}}

        <div class="form-group ">
            <label for="customer_type">ارز اصلی:</label>
            <select name="mainCurrency" id="mainCurrency" class="form-control select2_1 mainCurrency">
                @foreach($currencies as $currency)
                    <option @if(get_options("mainCurrency")->value('option_value') == $currency->currency_id) selected @endif value="{{ $currency->currency_id }}">{{ $currency->currency_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group ">
            <label for="mainStore">ارز اصلی:</label>
            <select name="mainStore" id="mainStore" class="form-control select2_1 mainStore">
                @foreach($stores as $store)
                    <option @if(get_options("mainStore")->value('option_value') == $store->store_id) selected @endif value="{{ $store->store_id }}">{{ $store->store_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6 col-lg-6 ">
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                اطلاعات <i class="fa fa-save" ></i>
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

