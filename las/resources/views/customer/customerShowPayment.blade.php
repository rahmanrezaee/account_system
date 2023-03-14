<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>شماره</th>
            <th>نام وتخلص</th>
            <th>نمبرتماس</th>
            <th>مجموعه پرداختی (افغانی)</th>
            <th>پرداخت فعلی (افغانی)</th>
            <th>بدهکاری</th>
            <th>علمیات</th>


        </tr>
        </thead>
        <tbody>
        @foreach($customer as $cus )

            <tr>
                <td>{{$cus->total_payment_id}} </td>
                <td>{{$cus->name}} </td>
                <td>{{$cus->phone}} </td>
                <td>{{$cus->total_payment}} </td>
                <td>{{$cus->current_payment}} </td>
                <td>{{($cus->total_payment-$cus->current_payment)}} </td>
                <td>

                    <a href="javascript:ajaxLoad('{{route('customer_get_payment',$cus->customer_id)}}')" class="btn btn-info btn-xs">پرداخت</a>
                    <a href="javascript:ajaxLoad('{{route('show_details_payment',$cus->total_payment_id)}}')"><span><i
                                    class="glyphicon glyphicon-eye-open "></i></span></a>
                    <a href="javascript:ajaxLoad('{{route('customer.payment_update',$cus->total_payment_id)}}')"
                       title=""><span><i
                                    class="glyphicon glyphicon-edit"></i></span></a>
                </td>
            </tr>
        @endforeach

        </tbody>


    </table>

</div>


<!-- Modal -->
<div class="modal fade " id="customer_payment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     style="margin-top:10%; ">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">پرداخت بدهی مشتری</h4>
            </div>
            <div class="modal-body" style="margin-right: 5%; margin-left: 5%;">

                <form method="post" id="frm"
                      action="{{route('customer.customer_payment')}}">
                    {{csrf_field()}}


                    <input type="hidden" id="total_payment_id" name="total_payment_id">
                    <input type="hidden" id="borrow" name="borrow">

                    <div class="input-group margin-bottom-20 col-md-12">
                        <label for="customer_type">نوعیت پرداخت مشتری</label>
                        <!-- /.input-group-btn -->
                        <select name="payment_type" id="payment_type" class="payment_type form-control ">
                            <option value="سالون ها"> سالون ها</option>
                            <option value="مینوی غذا"> مینوی غذا</option>
                            <option value="دیکوریشن"> دیکوریشن</option>
                            <option value="میوزیک"> میوزیک</option>
                            <option value="فلمبردار"> فلمبردار</option>
                            <option value="متفرقه">متفرقه</option>
                        </select>
                    </div>

                    <div class="form-group required" id="form-current_payment-error">
                        <label for="current_payment"> :پرداخت فعلی*</label>
                        <input type="number" name="current_payment" id="current_payment" class="form-control required">
                        <span id="current_payment-error" class="help-block"></span>
                    </div>
                    <div class="form-group  required" id="form-payment_date-error">
                        <label for="payment_date" class="control-label ">تاریخ پرداخت:*</label>
                        <div class="input-group">
                            <input type="text" class="form-control required" placeholder="روز/ماه/سال"
                                   id="jalali-datepicker"
                                   name="payment_date">
                            <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                        </div>
                        <span id="payment_date-error" class="help-block"></span>
                    </div>


                    <div class="modal-footer">

                        <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light"
                                id="btn_save">ذخیره
                            اطلاعات
                        </button>
                        <button type="button" class="btn btn-default btn-sm waves-effect waves-light"
                                data-dismiss="modal">منصرف
                            شدن
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">


    function setvale(val1, val2, val3) {
        $('#total_payment_id').val(val1);
        $('#current_payment').val(val2);
        $('#pay_date').val(val3);
    }

   /* $(document).on('submit', '#btn_save', function () {

        //alert("clicked");
        $('#customer_payment_modal').modal('hide');

    })*/


    $(document).ready(function () {



        $("#payment_type").change(function () {

            var pt = $(this).val();
            alert('this is result'+pt);
            var cusId = $(#customer_id).val();
            var total = $(#total_payment_id).val();

            $.ajax({
                url: 'customer/customerGetPayment',
                data:{"payment_type":pt,"customer_id":cusId,"total_payment_id":total},
                type: 'get',
                dataType: 'json',
                success: function (data) {

                    $('#current_payment').val(data.remain);
                }
            });

        });

    })
        /*function getCustomerPayment() {
            $(document).on('click', function () {

                var cus_id = $(this).data('data-remodal-id');
                $.ajax({
                    url: 'customer/customerGetPayment',
                    data:{"cut_id":cus_id,"asd_id":67},
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {

                        $('#total_payment_id').val(data.total_payment_id);
                        $('#current_payment').val(data.borrow);
                        $('#pay_date').val(data.date);

                    }
                })


            })


            $('#customer_payment_modal').modal('hide');
        }

*/
    $(document).ready(function () {
        //getModalId();
        datatable();


    });

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


