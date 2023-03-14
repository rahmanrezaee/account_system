
<div class="row">
    <h4 style="text-align: center">
        {{isset($panel_title) ?$panel_title :'ثبت انتقال محصولات بین گدام ها'}}
    </h4>

</div>
<div class="row">
    <div class="col-md-12">
        <form method="post" id="frm" action="{{ route('store.transStaff') }}" >
            {{csrf_field()}}
            <div class="form-row">

                <div class="form-group col-md-3 col-lg-3 col-sm-12">
                    <label for="stack_name">نام گدام:*</label>
                    <select required name="stack_name_sender" id="stack_name_sender" class="form-control select2_1 stack_name_sender">
                        @foreach($stores as $store)
                        <option value="{{$store->store_id}}"> {{$store->store_name}}</option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group col-md-3 col-lg-3 col-sm-12">
                    <label for="stack_name">نام گدام برای انتقال:*</label>
                    <select required name="stack_name_receiver" id="stack_name_receiver" class="form-control select2_1 stack_name_receiver">
                        @foreach($stores as $store)
                        <option value="{{$store->store_id}}"> {{$store->store_name}}</option>
                        @endforeach

                    </select>
                </div>

                <div class="form-group col-md-3 col-lg-3 col-sm-12" id="form-pr_date-error">
                    <label for="pr_date" class="control-label ">تاریخ:*</label>
                        <input type="text" value="" class="form-control date-picker required" placeholder="روز/ماه/سال"
                               name="pr_date"  autocomplete="off">
                  <!-- /.input-group -->
                    <span id="pr_date-error" class="help-block"></span>

                </div>


            </div><!--end row-->
            <div class="form-row">

                <div class="col-md-12 table-responsive">
                    <h5>اضافه نمودن معلومات بیشتر</h5>
                    <hr style="margin-top: 0px;">

                    <table class="table table-bordered table-striped autocomplete_table" style="margin-bottom: 8px;" id="autocomplete_table">
                        <thead>
                        <tr>
                            <td>حذف</td>
                            <th>نام محصول</th>
                            <th>کود محصول</th>
                            <th>تعداد</th>
                            <th>قیمت</th>


                        </tr>
                        </thead>

                        <tbody>
                        <tr id="row_1">
                            <td><button type="button" id="delete_1"  class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>
                            <td>
                                <input type="text" data-field-name="product_name" name="product_name[]" id="product_name_1" class=" form-control input-sm autocomplete_txt" autocomplete="off">
                                <input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_1" class=" form-control input-sm autocomplete_txt" autocomplete="off">
                                <input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id_1" class=" form-control input-sm autocomplete_txt" autocomplete="off">
                                <input type="hidden" data-field-name="unit_id" name="unit_id[]" id="unit_id_1" class=" form-control input-sm autocomplete_txt" autocomplete="off">
                            </td>
                            <td><input type="text" data-field-name="product_code" name="product_code[]" id="product_code_1" class="form-control input-sm autocomplete_txt"></td>
                            <td><input type="text" data-field-name="pquantity" name="product_quantity[]" id="product_quantity_1" class="form-control input-sm  autocomplete_txt quantity amount">
                            </td>
                            <td><input type="text" data-field-name="product_sale_price" name="product_price[]" id="product_price_1" class="form-control input-sm autocomplete_txt price"></td>
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
                            <td style="border: none;"></td>



                        </tr>
                        </tfoot>
                    </table>
                    <ul id="pr_ul">
                        <li>
                            <button type="submit" id="btn_save" class="btn btn-primary">انتقال اطلاعات</button>

                        </li>
                        <li>
                            <a href="javascript:ajaxLoad('sale_factor')" class="btn btn-danger">لغو</a>

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
        var rowcount,addBtn,tableBody,mount;




        addBtn=$('#addNew');
        rowcount=$('#autocomplete_table tbody tr').length+1;
        tableBody=$('#autocomplete_table');
        function formHtml() {
            //rowcount=rowcount+1;
            console.log(rowcount);
            html='<tr id="row_'+rowcount+'">';
            html +='<td><button type="button" id="delete_'+rowcount+'"  class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>';
            html +='<td><input type="text" data-field-name="product_name" name="product_name[]" id="product_name_'+rowcount+'" class=" form-control input-sm autocomplete_txt"><input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_'+rowcount+'" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id_'+rowcount+'" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="unit_id" name="unit_id[]" id="unit_id_'+rowcount+'" class=" form-control input-sm autocomplete_txt" autocomplete="off"></td>';
            html +='<td><input type="text" data-field-name="product_code" name="product_code[]" id="product_code_'+rowcount+'" class="form-control input-sm autocomplete_txt"></td>';
            html +='<td><input type="text" data-field-name="quantity" name="product_quantity[]" id="product_quantity_'+rowcount+'" class="form-control input-sm  autocomplete_txt quantity amount"></td>';
            html +='<td><input type="text" data-field-name="product_sale_price" name="product_price[]" id="product_price_'+rowcount+'" class="form-control input-sm autocomplete_txt price"></td>';
            html +='</tr>';
            rowcount++;
            return html;

        }

        function addNewRow() {
            var html=formHtml();
            console.log(html);
            tableBody.append(html);
        }
        function delete_Row() {

            var rowNo;
            id=$(this).attr('id');
            //console.log(id);
            id_arr=id.split("_");
            // console.log(id_arr);
            rowNo=id_arr[id_arr.length-1];
            if(rowNo>1){
                $('#row_'+rowNo).remove();

            }else{
                alert("شما نمی توانداین سطر را جذف کندید");
            }

            //console.log($(this).parent());
            // $(this).parent().parent().remove();

        }
        function getId() {
            var id,idArr;
            id=element.attr('id');
            idArr=id.split("_");
            return idArr[idArr.length-1];
        }

        function handleAutocomplete() {
            var fieldNam,currentEle;

            currentEle=$(this)
            fieldNam=currentEle.data('field-name');
            if(typeof fieldNam=='undefined'){
                return false;
            }
            currentEle.autocomplete({
                /* autofocus:true,
                 minLength:0,*/
                source:function (data,cb) {

                    $.ajax({
                        url:'{{route('sale_factor.searchResponseSele')}}',
                        method:'GET',
                        dataType:'json',
                        data:{
                            seleid:$("#stack_name_sender").val(),
                            name:data.term,
                            fieldName:fieldNam,

                    },
                    success:function (res) {
                        console.log(res);

                        var result;

                        result =[
                            {
                                label:'جنس به این نام یافت نشد!',
                                value:''
                            }
                        ];

                        if(res.length){
                            result=$.map(res,function(obj){

                                return {
                                    label:obj[fieldNam],
                                    value:obj[fieldNam],
                                    data:obj
                                };
                            });
                        }

                        cb(result);
                    },
                    error:function(data){
                        console.log(data);




                    }
                });
                },



                select: function( event, ui ) {
                    var data = ui.item.data;

                    id_arr = $(this).attr('id');
                    id = id_arr.split("_");
                    elementId = id[id.length-1];
                    $('#product_name_'+elementId).val(data.product_name);
                    $('#product_id_'+elementId).val(data.product_id);
                    $('#product_store_id_'+elementId).val(data.product_stor_id);
                    $('#unit_id_'+elementId).val(data.unit_id);
                    $('#product_unit_' + elementId).val(data.unit_name);
                    $('#product_code_'+elementId).val(data.product_code);
                    $('#product_price_'+elementId).val(data.product_sale_price);
                    $('#product_quantity_'+elementId).val(data.quantity);

                    mount = data.quantity;




                }
            });

        }

        function handleTotalRow() {
            var tr=$(this).parent().parent();

            var amount=tr.find('.quantity').val();

            var price=tr.find('.price').val();

            var total_amount=(amount*price);

            tr.find('.amount_total').val(roundPrice(total_amount,2));

            total();

        }
        function total() {
            var tr=$(this).parent().parent();
            var  total=0;
            $('.amount_total').each(function (i,e) {
                var amount=$(this).val()-0;
                total +=amount;
            });


            $('.total').val(roundPrice(total,2));
        }

        function registerEvent() {
            addBtn.on('click',addNewRow);
            $(document).on('click','.delete_row',delete_Row);
            $(document).on('focus','.autocomplete_txt',handleAutocomplete);
            $('tbody').delegate('.amount','keyup',handleTotalRow);

        }
        registerEvent();

        $(document).on("keyup",".quantity",function(){


            if($(this).val() > mount){


                $(this).css("borderColor","red");
                alert("  \n    دیتا وارده زیاد میباشد."+mount+"تعداد در گدام")

            }
            if($(this).val() <= mount){

                $(this).css("borderColor","green");
            }

        })


    });
    $(document).ready(function () {

        selectTwo();
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });

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
        $('.customer_type').change(function(){
            var customer_type=$(this).val();

            var _token=$('input[name="_token"]').val();

            $.ajax({
                url: "{{route('sale_factor.fetch')}}",
                method:'post',
                dataType:'json',
                data:{_token:_token,customer_type:customer_type},
                success:function (result) {
                    console.log(result);
                    $('#campany_name').html(result.option)

                    $.each(result,function(key, val){

                    })


                }


            })

        })
    });

    // customer type js
    $(document).ready(function () {
        $('.stack_name').change(function(){
            var stack_name=$(this).val();

            var _token=$('input[name="_token"]').val();

            $.ajax({
                url: "{{route('sale_factor.putID')}}",
                method:'post',
                dataType:'json',
                data:{_token:_token,stack_name:stack_name},
                success:function (result) {
                    console.log(result);
                    //$('#campany_name').html(result.option);

                    $.each(result,function(key, val){

                    });


                }


            });

        });
    });


    $(document).ready(function() {
        var oTable = $('#autocomplete_table').dataTable( {
            "order": [[ 0, "asc" ]],
            "paging":   true,
            "ordering": true,
            "info":     true,
            "pageLength": 50,
            "bLengthChange" : true,
            "processing": true,
            "serverSide": true,
            "pagingType": "full_numbers",
            "ajax": $.fn.dataTable.pipeline( {
                {{--url: "{{route('sale_factor.print_factor')}}",--}}
                pages: 100 // number of pages to cache
            } ),

            "aoColumns": [
                { "bSortable": true, "mDataProp": "SalonName"},
                { "bSortable": true, "mDataProp": "DistributorName" },
                { "bSortable": true, "mDataProp": "DSCName" },
                { "bSortable": true, "mDataProp": "BackBarCredits","sClass": "right" },
                { "bSortable": true, "mDataProp": "MarketingCredits","sClass": "right" },
                { "bSortable": true, "mDataProp": "ERetailCredits","sClass": "right" },
                { "bSortable": true, "mDataProp": "EBackBarCredits","sClass": "right" },
                { "bSortable": true, "mDataProp": "PromotionCredits","sClass": "right" },
                { "bSortable": true, "mDataProp": "EducationCredits","sClass": "right" },
                { "bSortable": false, "mDataProp": "SalonID",
                    "mRender": function (data) {
                        var EditLinkText = '<a class="noExport" href="/salon/salon_view.php?salonid=' + data + '&custservmain=1"><img width="20px" title="View Salon Account" src="../img/edit.png"></a>';
                        return EditLinkText;
                    } },

            ],

            "dom": '<"clear">T<"clear"><"clear">lfrtip',
            "oTableTools": {
                "sSwfPath": "/swf/copy_csv_xls_pdf.swf",
                "aButtons": [
                    {
                        "sExtends": "copy",
                        "oSelectorOpts": { filter: 'applied', order: 'current' },
                        "bShowAll": true
                    },
                    {
                        "sExtends": "xls",
                        "oSelectorOpts": { filter: 'applied', order: 'current' },
                        "sFileName": "SalonList_<?php echo date("Y-m-d_H-i")?>.xls",
                    },
                    {
                        "sExtends": "csv",
                        "oSelectorOpts": { filter: 'applied', order: 'current' },
                        "sFileName": "SalonList_<?php echo date("Y-m-d_H-i")?>.csv",
                    },
                    {
                        "sExtends": "print",
                        "oSelectorOpts": { filter: 'applied', order: 'current' },
                    },
                ]
            }
        } );



    } );


    function roundPrice(rnum, rlength) {
        var newnumber = Math.ceil(rnum * Math.pow(10, rlength-1)) / Math.pow(10, rlength-1);
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
