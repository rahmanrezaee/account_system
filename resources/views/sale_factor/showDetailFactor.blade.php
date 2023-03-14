<h4 class="" style="text-align: center">
    {{isset($panel_title) ?$panel_title :'جزییات فکتور'}}
</h4>

<div class="row">
    <div class="col-md-12">
        <form method="post" id="frm" action="{{isset($sale_factor) ?url('sale_factor/update/'.$sale_factor->sale_factor_id):route('sale_factor.create')}}" >
            {{isset($sale_factor) ?method_field('put') :''}}
            {{csrf_field()}}
            <div class="form-row">

                <div class="form-group col-md-3 col-lg-3 col-sm-12">
                    <label for="stack_name">نام گدام:*</label>
                    <select name="stack_name" id="stack_name" class="form-control select2_1 stack_name">
                        @foreach($stores as $store)
                            <option value="{{$store->store_id}}"> {{$store->store_name}}</option>
                        @endforeach

                    </select>
                </div>

            </div><!--end row-->
            <div class="form-row">

                <div class="col-md-12 table-responsive">

                    <table class="table table-bordered table-striped autocomplete_table" style="margin-bottom: 8px;" id="autocomplete_table">
                        <thead>
                        <tr>
                            <td>حذف</td>
                            <th>نام محصول</th>
                            <th>کود محصول</th>
                            <th>تعداد</th>
                            <th>قیمت (افغانی)</th>
                            <th>واحدمحصول</th>
                            <th>مجموع کل</th>


                        </tr>
                        </thead>

                        <tbody>

                        <?php $count = 1;?>

                        @foreach($saleProduct as $pro)
                            <tr id="row_<?php echo $count++;?>">
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
                            <td><input type="text" data-field-name="product_unit" name="product_unit[]" id="product_unit_1" class="form-control input-sm autocomplete_txt unit qty"></td>
                            <input type="hidden" data-field-name="pdesc" name="product_desc[]" id="product_desc_1" class="form-control input-sm autocomplete_txt">
                            <td><input type="text" data-field-name="ptotal_amount" name="product_total[]" id="product_total_1" class="form-control input-sm autocomplete_txt amount_total"></td>
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
                            <td style="border: none;"></td>
                            <td style="border: none;"></td>
                            <td style="border: none;"></td>
                            <td style="border: none;"  class="total padding-20">قمیت مجموع</td>

                            <td style="border: none;">
                                <div class="form-group required" id="form-pr_total-error">
                                    <input type="text"   name="pr_total" id="pr_total" class="form-control total required"
                                           title="قیمت کل" value="0">
                                    <span id="pr_total-error" class="help-block"></span>
                                </div>

                            </td>


                        </tr>
                        </tfoot>
                    </table>
                    <ul id="pr_ul">
                        <li>
                            <button type="submit" id="btn_save" class="btn btn-primary">ذخیره اطلاعات</button>

                        </li>
                        <li>
                            <a href="javascript:ajaxLoad('sale_factor')" class="btn btn-danger">لغو</a>

                        </li>

                    </ul>

                </div>
            </div>
        </form><!--</form>-->
    </div>
</div>



<script src="/js/jquery-ui.min.js"></script>

<script src="/js/printPreview.js"></script>

<script src="/js/jQuery.print.js"></script>
<script>

    $(document).ready(function () {

        $('#example').dataTable({
            "ordering": false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'pdf', 'print'
            ],
        });

        $(".showModel").click(function () {


            var d = $(this).attr("sale_factor_id");
            var c = $(this).attr("count");
            var t = $(this).attr("total");
            var p = $(this).attr("product_id");

            $("#total_price").val(t);
            $("#count").val(c);
            $("#factor_id").val(d);
            $("#product_id").val(p);

            $("#showDetail").modal("show")


        })

    });
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
            html += '<td><input type="text" data-field-name="product_unit" name="product_unit[]" id="product_unit_' + rowcount + '" class="form-control input-sm autocomplete_txt unit"></td>';
            html +='<input type="text" data-field-name="pdesc" name="product_desc[]" id="product_desc_'+rowcount+'" class="form-control input-sm autocomplete_txt">';
            html +='<td><input type="text" data-field-name="ptotal_amount" name="product_total[]" id="product_total_'+rowcount+'" class="form-control input-sm autocomplete_txt amount_total"></td>';
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
                        url:'{{route('sale_factor.search')}}',
                        method:'GET',
                        dataType:'json',
                        data:{
                            name:data.term,
                            fieldName:fieldNam,
                            store_id: $("#stack_name").val()

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
                    $('#product_price_'+elementId).val(new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(data.product_sale_price));
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
        jalali();

    });


</script>