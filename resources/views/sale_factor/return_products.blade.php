<div class="row">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :'ثبت فکتور خروجی ازگدام'}}
    </h4>

</div>
<div class="row">
    <div class="col-md-12">
        <form method="post" id="frm" action="{{url('sale_factor/returnFactor/'.$sale_factors->sale_factor_id)}}">
            {{isset($sale_factors) ?method_field('put') :''}}
            {{csrf_field()}}


            <div class="form-group col-md-2 col-lg-2 col-sm-12" id="form-pr_date-error">
                <label for="pr_date" class="control-label ">تاریخ:*</label>
                   <input type="date" class="form-control required" placeholder="روز/ماه/سال"
                           name="pr_date" value="{{ $sale_factors->sale_date }}" autocomplete="off">
                  <!-- /.input-group -->
                <span id="pr_date-error" class="help-block"></span>
            </div>

            <div class="form-group col-md-2 col-lg-2 col-sm-12">
                <label for="stack_name">نرخ ارز و ارز:*</label>

                <div class="input-group d-flex">
                    <input type="text" readonly value="{{ $sale_factors->currency_rate }}" required name="currency_rate"
                           id="currency_rate"
                           class="form-control select2_1 sale_factor_code">
                    <select class="form-control  no-padding" name="currency_id" id="currency_id">

                            <option disabled selected value="{{ $sale_factors->currency_id }}">
                                {{ getCurrencyName($sale_factors->currency_id)}}
                            </option>

                    </select>
                </div>


            </div>
            <input type="hidden" name="stack" id="stack_name" value="{{ $sale_factors->store_id }}">
            <div class="form-row">

                <div class="col-md-12 table-responsive">

                    <table class="table table-bordered table-striped autocomplete_table" style="margin-bottom: 8px;"
                           id="autocomplete_table">
                        <thead>
                        <tr>
                            <td>حذف</td>
                            <th>نام محصول</th>
                            <th>واحدمحصول</th>
                            <th>تعداد</th>
                            <th>فی قمیت</th>
                            <th>مجموع کل</th>

                        </tr>
                        </thead>

                        <tbody>

                        @foreach($sale_product as $key => $p)
                            <tr id="row_{{ $key+1 }}">
                                <td>
                                    <button type="button" id="delete_{{ $key+1 }}"
                                            class=" btn btn-danger btn-sm remove delete_row"><i
                                                class="glyphicon glyphicon-remove-sign"></i></button>
                                </td>
                                <td>
                                    <input type="text" data-field-name="product_name" name="product_name[]"
                                           id="product_name_{{ $key+1 }}"
                                           class=" form-control input-sm autocomplete_txt" readonly
                                           autocomplete="off" value="{{$p->product_name}}">
                                    <input type="hidden" data-field-name="product_id" name="product_id[]"
                                           id="product_id_{{ $key+1 }}" class=" form-control input-sm autocomplete_txt"
                                           autocomplete="off" value="{{$p->product_id}}">
                                    <input type="hidden" data-field-name="product_sale_id" name="product_sale_id[]"
                                           id="product_sale_id_{{ $key+1 }}"
                                           class=" form-control input-sm autocomplete_txt"
                                           autocomplete="off" value="{{$p->sales_id}}">

                                    <input type="hidden" data-field-name="unit_id" name="unit_id[]"
                                           id="unit_id_{{ $key+1 }}"
                                           class=" form-control input-sm autocomplete_txt"
                                           autocomplete="off" value="{{$p->main_unit_id}}">

                                </td>
                                <td><select  data-field-name="product_unit" name="product_unit[]"
                                             id="product_unit_{{ $key+1 }}" class="form-control input-sm  unit qty">

                                        <option data-mainId='{{ $p->exchange_unit_id }}'

                                                value='{{ $p->main_unit_id }}'>
                                            {{$p->unit_name}}

                                        </option>

                                    </select>

                                <td><input type="text" required data-field-name="pquantity" name="product_quantity[]"
                                           id="product_quantity_{{ $key+1 }}"
                                           class="form-control input-sm  autocomplete_txt quantity quantity_sale_factor"
                                           value="{{$p->quantity}}"></td>



                                <td><input type="text" data-field-name="product_sale_price" name="product_price[]"
                                           id="product_price_{{ $key+1 }}"
                                           class="form-control input-sm autocomplete_txt price"
                                           value="{{$p->unitprice}}"></td>


                                <td><input type="text" data-field-name="ptotal_amount" name="product_total[]"
                                           id="product_total_{{ $key+1 }}"
                                           class="form-control input-sm autocomplete_txt amount_total"
                                           value="{{$p->totalprice}}"></td>

                            </tr>
                        @endforeach

                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="border: none;">
                                <button type="button" class=" btn btn-primary btn-sm  " title="اضافه نمودن سطر جدید"
                                        id="addNew">
                                    <i class="glyphicon glyphicon-plus-sign"></i>
                                </button>
                            </td>
                            <td style="border: none;"></td>

                            <td style="border: none;" class="total"></td>
                            <td style="border: none;">


                            </td>


                            <td style="border:none;">
                                <div class="form-group required" id="form-pr_total-error">
                                    <label>قیمت کل بازگشت</label>
                                    <input type="text" name="pr_total" id="pr_total" required
                                           class="form-control total required"
                                           title="قیمت کل" value="{{ $sale_return }}">
                                    <span id="pr_total-error" class="help-block"></span>
                                </div>

                            </td>

                            <td style="border: none;">
                                <div class="form-group required" disabled="true" id="form-pr_payment-error">
                                    <label>قیمت پرداختی</label>
                                    <input type="text" disabledwe name="pr_payment" id="pr_payment" class="form-control required"
                                           title="قیمت پرداختی" value="{{ $sale_factors->recieption_price }}">
                                    <span id="pr_payment-error" class="help-block"></span>
                                </div>

                            </td>



                        </tr>
                        </tfoot>
                    </table>
                    <ul id="pr_ul">
                        <li>
                            <input type="hidden" name="printed" id="print-input" value="0">
                            <button type="submit"
                                    onclick="document.getElementById('print-input').value = 0;" id="btn_save" class="btn btn-primary btn_save">
                                ذخیره اطلاعات
                            </button>

                            <button type="submit"
                                    onclick="document.getElementById('print-input').value = 1;" id="btn_save_print" class="btn btn-primary btn_save">
                                ذخیره و پرنت
                            </button>

                        </li>
                        <li>
                            <a href="javascript:ajaxLoad('{{ route("sale_factor.list") }}')"
                               class="btn btn-danger">لغو</a>
                        </li>

                    </ul>

                </div>
            </div>
        </form><!--</form>-->
    </div>
</div> <!--</row>-->
<script src="/js/jquery-ui.min.js"></script>

<script src="/js/printPreview.js"></script>
<!--<script src="/js/jquery.print-preview.js"></script> -->
<script src="/js/jQuery.print.js"></script>


<script type="text/javascript">


    $(document).ready(function () {

        setEnabled($('.unit'), false);

        $(document).off("change").on("change", "select.unit", function () {





            let elementId = $(this).attr('id').split("_")[2];
            let productStoreId = $(this).attr('product-store-id');
            let mainId = $(this).find('option:selected').data("mainid");
            let unitId = $(this).val();


            $.ajax({
                url: '{{route('sale_factors.putMount')}}',
                method: 'GET',
                cache: false,
                async: true,
                dataType: 'json',
                data: {
                    related_unit: unitId,
                    main_id: mainId,
                    product_store_id: productStoreId
                },
                success: function (res) {

                    $('#product_quantity_' + elementId).val(res);

                },
                error: function (data) {
                    console.log(data);


                }

            });

        })




        var rowcount, addBtn, tableBody, mount;


        addBtn = $('#addNew');
        rowcount = $('#autocomplete_table tbody tr').length + 1;
        tableBody = $('#autocomplete_table');

        function formHtml() {
            //rowcount=rowcount+1;
            console.log(rowcount);
            html = '<tr id="row_' + rowcount + '">';
            html += '<td><button type="button" id="delete_' + rowcount + '"  class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>';
            html += '<td><input type="text" data-field-name="product_name" name="product_name[]" id="product_name_' + rowcount + '" class=" form-control input-sm autocomplete_txt"><input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="unit_id" name="unit_id[]" id="unit_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"></td>';
            html += '<td><select  data-field-name="product_unit" name="product_unit[]" id="product_unit_' + rowcount + '" class="form-control input-sm unit"></select></td>';

            html += '<td><input type="text" required data-field-name="quantity" name="product_quantity[]" id="product_quantity_' + rowcount + '" class="form-control input-sm  autocomplete_txt quantity quantity_sale_factor amount"></td>';
            html += '<td><input type="text"  required data-field-name="product_sale_price" name="product_price[]" id="product_price_' + rowcount + '" class="form-control input-sm autocomplete_txt price"></td>';
            html += '<td><input type="text" required data-field-name="ptotal_amount" name="product_total[]" id="product_total_' + rowcount + '" class="form-control input-sm autocomplete_txt amount_total"></td>';
            html += '</tr>';
            rowcount++;
            return html;

        }

        function addNewRow() {
            var html = formHtml();
            console.log(html);
            tableBody.append(html);
        }


        function delete_Row() {


            var rowNo;
            id = $(this).attr('id');
            //console.log(id);
            id_arr = id.split("_");

            // console.log(id_arr);
            rowNo = id_arr[id_arr.length - 1];
            $('#row_' + rowNo).remove();

        }

        function getId() {
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
        }

        function handleAutocomplete() {
            var fieldNam, currentEle;

            currentEle = $(this)
            fieldNam = currentEle.data('field-name');
            if (typeof fieldNam == 'undefined') {
                return false;
            }
            currentEle.autocomplete({
                /* autofocus:true,
                 minLength:0,*/
                source: function (data, cb) {

                    $.ajax({
                        url: '{{route('sale_factor.search')}}',
                        method: 'GET',
                        dataType: 'json',
                        data: {
                            name: data.term,
                            fieldName: fieldNam,
                            store_id: $("#stack_name").val()

                        },
                        success: function (res) {
                            console.log(res);

                            var result;

                            result = [
                                {
                                    label: 'جنس به این نام یافت نشد!',
                                    value: ''
                                }
                            ];

                            if (res.length) {
                                result = $.map(res, function (obj) {

                                    return {
                                        label: obj[fieldNam],
                                        value: obj[fieldNam],
                                        data: obj
                                    };
                                });
                            }

                            cb(result);
                        },
                        error: function (data) {
                            console.log(data);


                        }
                    });
                },


                select: function (event, ui) {
                    var data = ui.item.data;

                    id_arr = $(this).attr('id');
                    id = id_arr.split("_");
                    elementId = id[id.length - 1];
                    $('#product_name_' + elementId).val(data.product_name);
                    $('#product_id_' + elementId).val(data.product_id);
                    $('#product_store_id_' + elementId).val(data.product_stor_id);
                    $('#unit_id_' + elementId).val(data.unit_id);
                    $('#product_code_' + elementId).val(data.product_code);
                    $('#product_price_' + elementId).val(data.product_sale_price);
                    $('#product_quantity_' + elementId).val(data.quantity);

                    $.ajax({
                        url: '{{route('sale_factors.getUnits')}}',
                        method: 'GET',
                        cache: false,
                        async: true,
                        dataType: 'json',
                        data: {
                            unit_id: data.unit_id,
                        },
                        success: function (res) {

                            var result;

                            let innerHtml = "<option selected value='"+data.unit_id+"'> "+data.unit_name+"</option>";
                            if (res.length) {
                                for(let i in res){

                                    innerHtml +=   "<option data-mainId='"+res[i].main_unit_id+"' value='"+res[i].relate_unit_id+"'>"+res[i].unit_name+"</option>" ;
                                }
                            }
                            $('#product_unit_' + elementId).html(
                                innerHtml
                            );


                        },
                    });


                    mount = data.quantity;


                }
            });

        }

        function handleTotalRow() {
            var tr = $(this).parent().parent();
            var amount = tr.find('.quantity').val();

            var price = tr.find('.price').val();

            var total_amount = (amount * price);

            tr.find('.amount_total').val(roundPrice(total_amount, 2));

            total();

        }

        function total() {
            var tr = $(this).parent().parent();
            var total = 0;
            $('.amount_total').each(function (i, e) {
                var amount = $(this).val() - 0;


                total += amount;
            });


            $('.total').val(roundPrice(total, 2));
        }

        function registerEvent() {
            addBtn.on('click', addNewRow);
            $(document).on('click', '.delete_row', delete_Row);
            $(document).on('focus', '.autocomplete_txt', handleAutocomplete);

            $(document).on('keyup','.price',handleTotalRow);
            $(document).on('keyup','.quantity',handleTotalRow);


        }

        registerEvent();




    });
    $(document).ready(function () {

        selectTwo();
        jalali();

    });
    $(function () {
        $('#as_date').hide();
        $('#year').hide();
        $('#month').hide();
        $('#between_date').hide();
        $('#type').change(function () {

            if ($('#type').val() == 'month') {
                $('#between_date').hide();
                $('#year').hide();
                $('#as_date').hide();
                $('#month').show();
            } else if ($('#type').val() == 'week') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'day') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'year') {
                $('#month').hide();
                $('#as_date').hide();
                $('#year').show();
            } else if ($('#type').val() == 'date') {
                $('#month').hide();
                $('#year').hide();
                $('#as_date').show();
                $('#between_date').show();
            } else {
                $('#selection').hide();
            }
        });
    });

    // customer type js
    $(document).ready(function () {
        var customeid = $(".customer_id_div");
        var customename = $(".customer_name_div");

        var contentHidden = "<input type='hidden' value='2' name='customer_id'>";
        var contentC = $('.panel-user');


        $('.select2').css("width", "100% !important");
        $('.col-lg-3').css("min-height", "1px !important");
        var customer_type = $('.customer_type');


        if (customer_type.val() == 2) {

            contentC.html(contentHidden)
            customeid.hide();

        } else {

            customename.hide();

        }


        customer_type.change(function () {


            var customer_type = $(this).val();

            if (customer_type == 2) {

                $(".customer_name_input").val('');
                contentC.html(contentHidden)
                customename.show();
                customeid.hide();


            } else {

                contentC.html("")
                customename.hide();
                customeid.show();
                var _token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{route('sale_factor.fetch')}}",
                    method: 'post',
                    dataType: 'json',
                    data: {_token: _token, customer_type: customer_type},
                    success: function (result) {
                        console.log(result);
                        $('#customer_id').html(result.option)

                        $.each(result, function (key, val) {

                        })


                    }


                })
            }
        })
    });

    // customer type js
    $(document).ready(function () {


        $('#currency_id').change(function () {

            var _token = $('input[name="_token"]').val();



            $.ajax({

                url: "{{route('sale.currencyExchanger')}}",
                method: 'get',
                dataType: 'json',
                data: {_token: _token, currency_id: $(this).val()},
                success: function (result) {

                    $("#currency_rate").val(result);



                }


            });

        });


        $('.stack_name').change(function () {

            $('tbody').html("");
            var stack_name = $(this).val();

            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{route('sale_factor.putID')}}",
                method: 'post',
                dataType: 'json',
                data: {_token: _token, stack_name: stack_name},
                success: function (result) {
                    console.log(result);


                }


            });

        });
    });





    function roundPrice(rnum, rlength) {
        var newnumber = Math.ceil(rnum * Math.pow(10, rlength - 1)) / Math.pow(10, rlength - 1);
        var toTenths = newnumber.toFixed(rlength);
        return toTenths;
    }

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

        kamaDatepicker('pr_date', opt);

        /*================
          EDTITABEL TABLE
        * ===============*/
        $('.select2_1').select2();




    });


</script>
