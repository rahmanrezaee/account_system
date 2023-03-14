<div class="row">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :'ثبت فکتور خروجی ازگدام'}}
    </h4>

</div>
<div class="row">
    <div class="col-md-12">
        <form method="post" id="frm"
              action="{{isset($sale_factor) ?url('sale_factor/update/'.$sale_factor->sale_factor_id):route('sale_factor.create')}}">
            {{isset($sale_factor) ?method_field('put') :''}}
            {{csrf_field()}}
            <div class="form-row">

                <div class="form-group col-md-2 col-lg-2 col-sm-12 no-padding ">
                    <label for="customer_type">نوعیت مشتری:</label>
                    <select name="customer_type" id="customer_type" class="form-control customer_type no-padding">
                        <option value="-1">انتخاب کنند</option>
                        <option value="1">مشتری شرکتی</option>
                        <option value="2">مشتری متفرقه</option>
                    </select>

                </div>

                <div class="form-group col-md-2 col-lg-2 col-sm-12 customer_id_div">
                    <label for="customer_id">نام مشتری:*</label>
                    <select name="customer_id" id="customer_id" class="form-control select2_1 width-100">
                        <option value="0">نوعیت مشتریان تان را انتخاب کنید!</option>
                        @foreach ($customers as $customer)
                            <option value="{{$customer->customer_id}}"
                                    @if (isset($sale_factor) && $customer->id == $sale_factor->customer_id)
                                    selected
                                    @endif>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="panel-user"></div>

                <div class="form-group col-md-2 col-lg-2 col-sm-12 customer_name_div">
                    <label for="customer_id">نام مشتری:*</label>
                    <input type="text" class="form-control required customer_name_input" placeholder="نام را وارد کنید"
                           name="customer_name" autocomplete="off">
                    <!-- /.input-group -->
                    <span id="customer_name-error" class="help-block"></span>
                </div>

                <div class="form-group col-md-2 col-lg-2 col-sm-12">
                    <label for="stack_name">نام گدام:*</label>
                    <select name="stack_name" id="stack_name" class="form-control select2_1 stack_name">
                        @foreach($stores as $store)
                            <option value="{{$store->store_id}}"> {{$store->store_name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2 col-lg-2 col-sm-12">
                    <label for="pr_date" class="control-label ">تاریخ:*</label>
                    <input type="date" class="form-control required " placeholder="روز/ماه/سال"
                           name="pr_date"  value="{{ date("Y-m-d") }}" autocomplete="off">

                    <span id="pr_date-error" class="help-block"></span>
                </div>

                <div class="form-group col-md-1 col-lg-1 col-sm-12 no-padding">
                    <label for="stack_name">شماره فکتور</label>
                    <input type="text" value="{{ ++$lastFactorCode }}" required name="sale_factor_code"
                           id="sale_factor_code"
                           class="form-control select2_1 sale_factor_code">
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
                            <th>واحدمحصول</th>
                            <th>تعداد</th>
                            <th>فی مالیات</th>
                            <th>فی قمیت</th>
                            <th>مجموع کل</th>

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
                                       id="product_name_1" class=" form-control input-sm autocomplete_txt"
                                       autocomplete="off">


                                <input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_1"
                                       class=" form-control input-sm autocomplete_txt" autocomplete="off">


                                <input type="hidden" data-field-name="product_buy_price_id" name="product_buy_price_id[]" id="product_buy_price_id_1"
                                       class=" form-control input-sm autocomplete_txt" autocomplete="off">


                                <input type="hidden" data-field-name="product_store_id" name="product_store_id[]"
                                       id="product_store_id_1" class="form-control input-sm autocomplete_txt"
                                       autocomplete="off">
                                <input type="hidden" data-field-name="unit_id" name="unit_id[]" id="unit_id_1"
                                       class=" form-control input-sm autocomplete_txt" autocomplete="off">
                            </td>
                            <td><input type="text" data-field-name="product_code" name="product_code[]"
                                       id="product_code_1" class="form-control input-sm autocomplete_txt"></td>

                            <td><select  data-field-name="product_unit" name="product_unit[]"
                                        id="product_unit_1" class="form-control input-sm  unit qty"></select>

                            </td>

                            <td><input type="text" data-field-name="pquantity" name="product_quantity[]"
                                       id="product_quantity_1"
                                       class="form-control input-sm  autocomplete_txt quantity quantity_sale_factor amount">
                            </td>
                            <td><input type="text" pattern="[0-9]|[1-9][0-9]|100" data-field-name="product_tex"
                                       name="product_tex[]"
                                       id="product_tex_1" class="form-control input-sm autocomplete_txt product_tex">
                            </td>

                            <td><input type="text" data-field-name="product_sale_price" name="product_price[]"
                                       id="product_price_1" class="form-control input-sm autocomplete_txt price"></td>


                            <input type="hidden" data-field-name="pdesc" name="product_desc[]" id="product_desc_1"
                                   class="form-control input-sm autocomplete_txt">


                            <td><input type="text" data-field-name="ptotal_amount" name="product_total[]"
                                       id="product_total_1" class="form-control input-sm autocomplete_txt amount_total">
                            </td>
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

                            <td style="border: none;" class="total"></td>
                            <td style="border: none;" class="total"></td>
                            <td style="border: none;">
                                <div class="form-group required" id="form-discount-error">
                                    <label>تخفیف حنس</label>
                                    <input type="number" name="discount" id="discount" class="form-control required"
                                           title="تخفیف حنس" value="0">
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
                                <div class="form-group required" id="form-total_tex-error">
                                    <label>مجموعه مالیات</label>
                                    <input type="text" name="total_tex" id="total_tex"
                                           class="form-control required total_tex"
                                           title="تخفیف حنس" value="0">
                                    <span id="total_tex-error" class="help-block"></span>
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
                            <input type="hidden" name="printed" id="print-input" value="0">
                            <button type="submit"
                                    onclick="document.getElementById('print-input').value = 0;"
                                    class="btn btn-primary btn_save btn-sm">
                                ذخیره اطلاعات
                            </button>

                            <button type="submit"
                                    onclick="document.getElementById('print-input').value = 1;"
                                    class="btn btn-primary btn_save btn-sm">
                                ذخیره و پرنت
                            </button>

                        </li>
                        <li>
                            <a href="javascript:ajaxLoad('{{ route('sale_factor.list') }}')"
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


        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });

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
            html += '<td><input type="text" data-field-name="product_name" name="product_name[]" id="product_name_' + rowcount + '" class=" form-control input-sm autocomplete_txt">' +
                '<input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off">' +
                '<input type="hidden" data-field-name="product_buy_price_id" name="product_buy_price_id[]" id="product_buy_price_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off">' +
                '<input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="unit_id" name="unit_id[]" id="unit_id_' + rowcount + '" class=" form-control input-sm autocomplete_txt" autocomplete="off"></td>';
            html += '<td><input type="text" data-field-name="product_code" name="product_code[]" id="product_code_' + rowcount + '" class="form-control input-sm autocomplete_txt"></td>';
            html += '<td><select  data-field-name="product_unit" name="product_unit[]" id="product_unit_' + rowcount + '" class="form-control input-sm unit"></select></td>';
            html += '<td><input type="text" data-field-name="quantity" name="product_quantity[]" id="product_quantity_' + rowcount + '" class="form-control input-sm  autocomplete_txt quantity_sale_factor quantity amount"></td>';
            html += '<td><input type="text" data-field-name="product_tex" name="product_tex[]" id="product_tex_' + rowcount + '" class="form-control input-sm autocomplete_txt product_tex"></td>';
            html += '<td><input type="text" data-field-name="product_sale_price" name="product_price[]" id="product_price_' + rowcount + '" class="form-control input-sm autocomplete_txt price"></td>';
            html += '<input type="text" data-field-name="pdesc" name="product_desc[]" id="product_desc_' + rowcount + '" class="form-control input-sm autocomplete_txt">';
            html += '<td><input type="text" data-field-name="ptotal_amount" name="product_total[]" id="product_total_' + rowcount + '" class="form-control input-sm autocomplete_txt amount_total"></td>';
            html += '</tr>';
            rowcount++;
            return html;

        }

        function addNewRow() {
            var html = formHtml();

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

        function getId() {
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1];
        }

        function handleAutocomplete() {

            var fieldNam, currentEle;

            currentEle = $(this);
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
                        cache: false,
                        async: true,
                        dataType: 'json',
                        data: {
                            name: data.term,
                            fieldName: fieldNam,
                            store_id: $("#stack_name").val()

                        },
                        success: function (res) {


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
                    $('#product_name_' + elementId).val(data.pp);
                    $('#product_store_id_' + elementId).val(data.product_stor_id);
                    $('#unit_id_' + elementId).val(data.unit_id);
                    $('#product_id_' + elementId).val(data.product_id);
                    $('#product_unit_' + elementId).attr("product-store-id",data.product_stor_id);
                    $('#product_quantity_' + elementId).val(data.quantity);

                    // $('#product_unit_' + elementId).val(data.unit_name);

                    $('#product_code_' + elementId).val(data.product_code);
                    $('#product_price_' + elementId).val(data.default_sale_product);
                    $('#product_bprice_' + elementId).val(data.product_buy_price);

                    $('#product_tex_' + elementId).val(data.text_mount);


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

            var tex = tr.find('.product_tex').val();

            var total_amount = (amount * price);

            tr.find('.amount_total').val(roundPrice(total_amount, 2));


            let total_tex_mount = 0;

            $('.product_tex').each(function (i, e) {

                var quentity_per_row = $(this).parent().parent().find('.quantity').val();
                var price_per_row = $(this).parent().parent().find('.price').val();

                var amount = (price_per_row / 100) * this.value * quentity_per_row;

                total_tex_mount += amount;

            });
            //
            $('.total_tex').val(roundPrice(total_tex_mount, 2));
            //
            total();

        }

        function total() {

            var tr = $(this).parent().parent();
            var total = 0;
            $('.amount_total').each(function (i, e) {
                var amount = $(this).val() - 0;
                total += amount;
            });


            let textValue = $('.total_tex').val();


            $('.total').val(roundPrice((parseFloat(total) + parseFloat(textValue)), 2));


        }

        function registerEvent() {

            addBtn.on('click', addNewRow);
            $(document).on('click', '.delete_row', delete_Row);
            $(document).on('focus', '.autocomplete_txt', handleAutocomplete);
            $(document).on('keyup', '.amount', handleTotalRow);
            $(document).on('keyup', '.price ', handleTotalRow);
            $(document).on('keyup', '.product_tex', handleTotalRow);

        }

        registerEvent();


        $('.quantity_sale_factor').off();

        $(document).on("keyup", ".quantity_sale_factor", function () {


            if ($(this).val() > mount) {

                $(this).css("borderColor", "red");
                // alert("  \n    دیتا وارده زیاد میباشد." + mount + "تعداد در گدام")

            }
            if ($(this).val() <= mount) {

                $(this).css("borderColor", "green");
            }

        })




    });
    $(document).ready(function () {

        selectTwo();



    });

    // customer type js
    $(document).ready(function () {

        var customeid = $(".customer_id_div");
        var customename = $(".customer_name_div");

        var contentHidden = "<input type='hidden' value='2' name='customer_id'>";
        var contentC = $('.panel-user');
        //
        //
        // $('.select2').css("width", "100% !important");
        // $('.col-lg-3').css("min-height", "1px !important");

        customeid.hide();
        customename.hide();


        $('.customer_type').change(function () {


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

                    cache: false,
                    async: true,
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

                cache: false,
                async: true,
                url: "{{route('sale.currencyExchanger')}}",
                method: 'get',
                dataType: 'json',
                data: {_token: _token, currency_id: $(this).val()},
                success: function (result) {

                    $("#currency_rate").val(result)
                }


            });

        });


        $('.stack_name').change(function () {
            var stack_name = $(this).val();

            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{route('sale_factor.putID')}}",
                method: 'post',
                dataType: 'json',

                cache: false,
                async: true,
                data: {_token: _token, stack_name: stack_name},
                success: function (result) {
                    console.log(result);
                    //$('#campany_name').html(result.option);

                    $.each(result, function (key, val) {

                    });


                }


            });

        });
    });


    $(document).ready(function () {
        var oTable = $('#autocomplete_table').dataTable({
            "order": [[0, "asc"]],
            "paging": true,
            "ordering": true,
            "info": true,
            "pageLength": 50,
            "bLengthChange": true,
            "processing": true,
            "serverSide": true,
            "pagingType": "full_numbers",
            "ajax": $.fn.dataTable.pipeline({
                {{--url: "{{route('sale_factor.print_factor')}}",--}}
                pages: 100 // number of pages to cache
            }),

            "aoColumns": [
                {"bSortable": true, "mDataProp": "SalonName"},
                {"bSortable": true, "mDataProp": "DistributorName"},
                {"bSortable": true, "mDataProp": "DSCName"},
                {"bSortable": true, "mDataProp": "BackBarCredits", "sClass": "right"},
                {"bSortable": true, "mDataProp": "MarketingCredits", "sClass": "right"},
                {"bSortable": true, "mDataProp": "ERetailCredits", "sClass": "right"},
                {"bSortable": true, "mDataProp": "EBackBarCredits", "sClass": "right"},
                {"bSortable": true, "mDataProp": "PromotionCredits", "sClass": "right"},
                {"bSortable": true, "mDataProp": "EducationCredits", "sClass": "right"},
                {
                    "bSortable": false, "mDataProp": "SalonID",
                    "mRender": function (data) {
                        var EditLinkText = '<a class="noExport" href="/salon/salon_view.php?salonid=' + data + '&custservmain=1"><img width="20px" title="View Salon Account" src="../img/edit.png"></a>';
                        return EditLinkText;
                    }
                },

            ],

            "dom": '<"clear">T<"clear"><"clear">lfrtip',
            "oTableTools": {
                "sSwfPath": "/swf/copy_csv_xls_pdf.swf",
                "aButtons": [
                    {
                        "sExtends": "copy",
                        "oSelectorOpts": {filter: 'applied', order: 'current'},
                        "bShowAll": true
                    },
                    {
                        "sExtends": "xls",
                        "oSelectorOpts": {filter: 'applied', order: 'current'},
                        "sFileName": "SalonList_<?php echo date("Y-m-d_H-i")?>.xls",
                    },
                    {
                        "sExtends": "csv",
                        "oSelectorOpts": {filter: 'applied', order: 'current'},
                        "sFileName": "SalonList_<?php echo date("Y-m-d_H-i")?>.csv",
                    },
                    {
                        "sExtends": "print",
                        "oSelectorOpts": {filter: 'applied', order: 'current'},
                    },
                ]
            }
        });


    });


    function roundPrice(rnum, rlength) {
        var newnumber = Math.ceil(rnum * Math.pow(10, rlength - 1)) / Math.pow(10, rlength - 1);
        var toTenths = newnumber.toFixed(rlength);
        return toTenths;
    }

    $(document).ready(function () {

        $('.select2_1').select2();



    });


</script>
