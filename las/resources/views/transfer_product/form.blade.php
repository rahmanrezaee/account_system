<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-12 col-lg-12 col-md-offset-3">
    <form method="post" id="frm"
          action="">
        {{csrf_field()}}
        <div class="row">
            <div class="col-sm-6 col-lg-6">
                <div class="form-group required" id="form-sender_id-error">
                    <label for="sender_id">فروشگاه فرستنده</label>
                    <select name="sender_id" id="sender_id" class="form-control required ">
                    </select>
                    <span id="sender_id-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-6 col-lg 6">
                <div class="form-group required" id="form-reciever_id-error">
                    <label for="reciever_id">فروشگاه دریافت کننده</label>
                    <select name="reciever_id" id="reciever_id" class="form-control required ">
                    </select>
                    <span id="reciever_id-error" class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-lg-6">
                <div class="form-group  required" id="form-transfer_product_date-error">
                    <label for="transfer_product_date" class="control-label ">تاریخ انتقال:*</label>
                    <div class="input-group">
                        <input type="text" class="form-control required" placeholder="روز/ماه/سال"
                               id="jalali-datepicker" name="transfer_product_date"
                               value="{{old('transfer_product_date')}}">
                        <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                    </div>
                    <span id="transfer_product_date-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6">
                <div class="form-group required" id="form-product_name-error">
                    <label for="product_name">نام محصول</label>
                    <input type="number" name="product_name" id="product_name" class="form-control required"
                           value="{{old('product_name')}}" >
                    <span id="product_name-error" class="help-block"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-lg-6">
                <div class="form-group required" id="form-product_quantity-error">
                    <label for="product_quantity">مقدار محصول</label>
                    <input type="number" name="product_quantity" id="product_quantity" class="form-control required"
                           value="{{old('product_quantity')}}" >
                    <span id="product_quantity-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-6">
                <div class="form-group required" id="form-transfer_product_description-error">
                    <label for="transfer_product_description">توضیحات:*</label>
                    <textarea type="text" class="form-control required" id="transfer_product_description" name="transfer_product_description"
                              value="" cols="4"
                              placeholder="توضیحات"></textarea>
                    <span id="transfer_product_description-error" class="help-block"></span>
                </div>
            </div>
        </div>

        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('transfer_product.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
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

