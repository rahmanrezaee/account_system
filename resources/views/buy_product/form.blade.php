<div class="row">

</div>
<div class="row">
    <div class="col-md-12">
        <form action="{{route('buy_product.create')}}" method="post" id="frm">
            {{csrf_field()}}
            <div class="form-row">

                <div class="form-group col-md-2 col-lg-2 col-sm-12">
                    <label for="stack_name">شماره فکتور:*</label>
                    <input type="text" placeholder="شماره فکتور" value="{{ ++$lastFactorCode }}" required name="sale_factor_code"
                           id="sale_factor_code" class="form-control select2_1 sale_factor_code">

                </div>

                <div class="form-group col-md-2">
                    <label for="company_name">نام شرکت:*</label>

                    <select name="company_name" id="company_name" class="form-control select2_1 company_name">

                        @foreach($companyes as $companye)
                            <option value="{{ $companye->company_id }}">{{ $companye->company_name }}</option>
                        @endforeach
                    </select>

                    {{--<input type="text" name="company_name" id="company_name" class="form-control" placeholder="نام شرکت">--}}

                </div>
                <div class="form-group col-md-2">
                    <label for="stack_name">نام گدام:*</label>
                    <select name="stack_name" id="stack_name" class="form-control select2_1 stack_name">

                        @foreach($stores as $store)
                            <option value="{{ $store->store_id }}">{{ $store->store_name }}</option>
                        @endforeach
                    </select>

                </div>


                <div class="form-group col-md-2 col-lg-2 col-sm-12" >
                    <label for="pr_date" class="control-label ">تاریخ:*</label>
                    <input type="date" class="form-control required" placeholder="روز/ماه/سال"
                           name="pr_date" value="{{ date('Y-m-d') }}"  autocomplete="off">

                    <span id="pr_date-error" class="help-block"></span>
                </div>



                <div class="form-group col-md-2 col-lg-2 col-sm-12">
                    <label for="stack_name">نرخ ارز و ارز:*</label>

                    <div class="input-group d-flex">
                        <input type="text" value="1" required name="currency_rate"
                               id="currency_rate"
                               class="form-control select2_1 sale_factor_code">
                        <select class="form-control no-padding" name="currency_id" id="currency_id">
                            @foreach($currencies as $currency)
                                <option @if(get_options("mainCurrency")->value('option_value') == $currency->currency_id) selected
                                        @endif value="{{ $currency->currency_id }}">{{ $currency->currency_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div><!--end row-->
            <div class="form-row">

                <div class="col-md-12 table-responsive">
                    <h5>اضافه نمودن معلومات بیشتر</h5>
                    <hr style="margin-top: 0px;">
                    <table class="table table-bordered table-striped autocomplete_table" style="margin-bottom: 8px;"
                           id="autocomplete_table">
                        <thead>
                        <tr>
                            <td>حذف</td>
                            <th>نام محصول</th>
                            <th>کود محصول</th>
                            <th>واحد محصول</th>
                            <th>تعداد</th>
                            <th>قیمت خرید</th>
                            <th>مجموعه</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr id="row_1">
                            <td>
                                <button type="button" id="delete_1" class=" btn btn-danger btn-sm remove delete_row"><i
                                            class="glyphicon glyphicon-remove-sign"></i></button>
                            </td>
                            <td>

                                <input type="text" data-field-name="product_name" name="product_name[]"
                                       id="product_name_1" class="form-control input-sm autocomplete_txt  "
                                       autocomplete="off">

                                <input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_1"
                                       class=" form-control input-sm autocomplete_txt" autocomplete="off">

                                <input type="hidden" data-field-name="product_store_id" name="product_store_id[]"
                                       id="product_store_id_1" class=" form-control input-sm autocomplete_txt"
                                       autocomplete="off">
                            </td>


                            <td><input type="text" data-field-name="product_code" name="product_code[]"
                                       id="product_code_1" class="form-control input-sm autocomplete_txt"></td>

                            <td>
                                <select  data-field-name="product_unit" name="product_unit_exchange[]"
                                         id="product_unit_1" class="form-control input-sm  unit qty"></select>

                            <td><input type="text" data-field-name="pquantity" required name="product_quantity[]"
                                       id="product_quantity_1"
                                       class="form-control input-sm  autocomplete_txt quantity amount qty"></td>

                            <td>

                                <input type="text" data-field-name="product_buy_price" name="product_bprice[]"
                                       id="product_buy_1" class="form-control input-sm autocomplete_txt buy_price qty">

                            </td>

                            <td><input type="text" data-field-name="ptotal_amount" name="product_total[]"
                                       id="product_total_1" class="form-control input-sm autocomplete_txt amount_total">
                            </td>

                        </tr>

                        </tr>

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
                            <td style="border: none;"></td>
                            <td style="border: none;" class="total"></td>
                            <td style="border: none;">
                                <div class="form-group required" id="form-discount-error">

                                    <label>تخفیف جنس</label>
                                    <input type="text" name="discount" id="discount" class="form-control required"
                                           title="تخفیف جنس" value="0">
                                    <span id="discount-error" class="help-block"></span>
                                </div>

                            </td>
                            <td style="border: none;">
                                <div class="form-group required" id="form-pr_payment-error">
                                    <label>قیمت پرداختی</label>
                                    <input type="text" name="pr_payment" id="pr_payment" class="form-control required"
                                           title="قیمت پرداختی" value="0">
                                    <span id="pr_payment-error" class="help-block"></span>
                                </div>


                            </td>
                            <td style="border: none;">

                                <div class="form-group required" id="form-pr_total-error">

                                    <label>قیمت کل</label>
                                    <input type="text" name="pr_total" id="pr_total" class="form-control total required"
                                           title="قیمت کل" value="0">
                                    <span id="pr_total-error" class="help-block"></span>
                                </div>


                            </td>

                        </tr>
                        </tfoot>
                    </table>
                    <ul id="pr_ul">
                        <li>
                            <button type="submit" class=" btn btn-primary btn-sm space" id="btn_save">
                                <span style="margin-right: 4px;">ذخیره اطلاعات</span>
                            </button>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('buy_factor.list')}}')"
                               class="btn  btn-default btn-sm ">
                                <span style="margin-right: 4px;">منصرف شدن</span>
                            </a>
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


        selectTwo();
        jalali();

    });
    $(document).ready(function () {
        var rowcount, addBtn, tableBody;

        addBtn = $('#addNew');
        rowcount = $('#autocomplete_table tbody tr').length + 1;
        tableBody = $('#autocomplete_table');

        function formHtml() {
            //rowcount=rowcount+1;
            console.log(rowcount);
            html = '<tr id="row_' + rowcount + '">';
            html += '<td><button type="button" id="delete_' + rowcount + '"  class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>';
            html += '<td><input type="text" data-field-name="product_name" name="product_name[]" id="product_name_' + rowcount + '" class=" form-control input-sm autocomplete_txt"><input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"></td>';

            html += '<td><input type="text" data-field-name="product_code" name="product_code[]" id="product_code_' + rowcount + '" class="form-control input-sm autocomplete_txt"></td>';
            html += '<td><select  data-field-name="product_unit" name="product_unit_exchange[]" id="product_unit_' + rowcount + '" class="form-control input-sm unit"></select></td>';
            html += '<td><input type="text" required data-field-name="quantity" name="product_quantity[]" id="product_quantity_' + rowcount + '" class="form-control input-sm  autocomplete_txt quantity amount"></td>';
            html += '<td><input type="text" data-field-name="product_buy_price" name="product_bprice[]" id="product_buy_' + rowcount + '" class="form-control input-sm autocomplete_txt buy_price"></td>';
            html += '<td><input type="text" data-field-name="ptotal_amount" name="product_total[]" id="product_total_' + rowcount + '" class="form-control input-sm autocomplete_txt amount_total"></td>';
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
            if (rowNo > 1) {
                $('#row_' + rowNo).remove();

            } else {
                alert("شما نمی توانداین سطر را جذف کندید");
            }

            //console.log($(this).parent());
            // $(this).parent().parent().remove();

        }

        function handleAutocomplete() {
            var fieldNam, currentEle;
            currentEle = $(this);
            fieldNam = currentEle.data('field-name');
            console.log(fieldNam);
            if (typeof fieldNam == 'undefined') {
                return false;
            }
            currentEle.autocomplete({
                /* autofocus:true,
                 minLength:0,*/

                classes: {

                    "ui-autocomplete": "load",

                },

                source: function (data, cb) {


                    $.ajax({
                        url: '{{route('buy_product_search')}}',
                        method: 'GET',
                        dataType: 'json',

                        cache: false,
                        async: true,
                        data: {
                            name: data.term,
                            fieldName: fieldNam,


                        },
                        success: function (res) {


                            var result;

                            result = [
                                {
                                    label: 'there is no matching record for' + data.term,
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
                    /*$('#product_store_id_'+elementId).val(data.product_store_id);*/
                    // $('#product_unit_' + elementId).val(data.unit_name);
                    $('#product_code_' + elementId).val(data.product_code);
                    $('#product_store_id_' + elementId).val(data.product_stor_id);
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


                }
            });

        }


        function handleTotalRow() {

            var tr = $(this).parent().parent();
            var amount = tr.find('.quantity').val();

            var price = tr.find('.buy_price').val();

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
            $(document).on( 'keyup','.amount', handleTotalRow);
            $(document).on('keyup', '.buy_price', handleTotalRow);

        }
        registerEvent();

    });

    function roundPrice(rnum, rlength) {
        var newnumber = Math.ceil(rnum * Math.pow(10, rlength - 1)) / Math.pow(10, rlength - 1);
        var toTenths = newnumber.toFixed(rlength);
        return toTenths;
    }

    $(document).ready(function () {


        $('.dynamic').change(function () {
            var buy_factor_id = $(this).val();
            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{route('buy_product.fetch')}}",
                method: 'post',
                dataType: 'json',
                cache: false,
                async: true,
                data: {_token: _token, buy_factor_id: buy_factor_id},
                success: function (result) {

                    console.log(result)
                    $.each(result, function (key, val) {


                        if (key == 'company_name') {

                            $('#company_name').val(val);

                        } else if (key == 'store_name') {

                            $('#stack_name').val(val);

                        } else if (key == 'total_payment') {

                            $("#total_payment_factor").text(val);

                        } else {

                            $('#factor_date').val(val);

                        }

                    })
                }

            })

        })
    })
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

    $(document).ready(function () {


        $('#currency_id').change(function () {

            var _token = $('input[name="_token"]').val();

            $.ajax({

                url: "{{route('sale.currencyExchanger')}}",
                method: 'get',
                dataType: 'json',
                data: {_token: _token, currency_id: $(this).val()},
                success: function (result) {

                    $("#currency_rate").val(result)
                }


            });

        });

        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });



    })




</script>
