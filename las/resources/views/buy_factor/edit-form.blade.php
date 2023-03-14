<div class="row">

</div>
<div class="row">
    <div class="col-md-12">
        <form action="{{route('buy_factor.edit',$id)}}" method="post" id="frm">
            {{csrf_field()}}
            <div class="form-row">
                <div class="form-group col-md-3">


                    <label for="factor_number">شماره فاکتور:*</label>
                    <select  name="factor_number" id="factor_number" class="form-control select2_1 dynamic" data-state="company">
                        <option value="0" disabled="true" selected="true">شماره فاکتور را انتخاب کنید</option>
                        @foreach($bfactores as $key=>$value)

                            <option value="{{$value->buy_factor_id}}" {{(isset($id) && $value->buy_factor_id==$id)?'selected'.'='.'selected':''}} >
                                {{$value->factor_code}}</option>

                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="company_name">نام شرکت:*</label>
                    <input type="text" name="company_name" id="company_name" class="form-control" placeholder="نام شرکت" disabled="true" value="{{$factor->company->company_name}}">

                </div>
                <div class="form-group col-md-3">
                    <label for="stack_name">نام گدام:*</label>
                    <input type="text" name="stack_name" id="stack_name" class="form-control" placeholder="نام گدام" disabled="true" value="{{$factor->stack->store_name}}">
                </div>


                <!--date -->
                <div class="form-group col-md-3">
                    <label for="pr_date" class="control-label ">تاریخ:*</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="تاریخ خریداری شده" id="factor_date" name="pr_date" disabled="true" value="{{$factor->buy_date}}">
                        <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                    </div>
                    <!-- /.input-group -->
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
                            <th>قمیت فروش</th>
                            <th>قیمت خرید</th>


                        </tr>
                        </thead>

                        <tbody>
                       @foreach($buy_factor as $b)
                           <tr id="row_1">
                               <td><button type="button" id="delete_1"  class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>
                               <td>
                                   <input type="text" data-field-name="product_name" name="product_name[]" id="product_name_1" class=" form-control input-sm autocomplete_txt" autocomplete="off" value="{{$b->product_name}}">
                                   <input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_1" class=" form-control input-sm autocomplete_txt" autocomplete="off" value="{{$b->product_id}}">
                                   <input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id_1" class=" form-control input-sm autocomplete_txt" autocomplete="off" value="{{$b->product_stor_id}}">
                               </td>
                               <td><input type="text" data-field-name="product_code" name="product_code[]" id="product_code_1" class="form-control input-sm autocomplete_txt" value="{{$b->product_code}}">
                                   <input type="hidden" data-field-name="product_code" name="buy_product_id[]" id="product_code_1" class="form-control input-sm autocomplete_txt" value="{{$b->buy_product_id}}">
                               </td>
                               <td><input type="text" data-field-name="pquantity" name="product_quantity[]" id="product_quantity_1" class="form-control input-sm  autocomplete_txt quantity amount" value="{{$b->quantity}}"></td>
                               <td><input type="text" data-field-name="product_sale_price" name="product_price[]" id="product_price_1" class="form-control input-sm autocomplete_txt price" value="{{$b->sale_price}}"></td>
                               <td><input type="text" data-field-name="product_buy_price" name="product_bprice[]" id="product_buy_1" class="form-control input-sm autocomplete_txt price" value="{{$b->buy_price}}"></td></tr>
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
                        </tr>
                        </tfoot>
                    </table>
                    <ul id="pr_ul">
                        <li><button type="submit" class=" btn btn-primary btn-sm space" id="btn_save">
                                <span style="margin-right: 4px;">ذخیره اطلاعات</span>
                            </button></li>
                        <li><a href="javascript:ajaxLoad('{{route('buy_factor.list')}}')" class="btn  btn-default btn-sm ">
                                <span style="margin-right: 4px;">منصرف شدن</span></a></li>
                        <li class="pull-left"><div class="form-group form-inline ">

                                <input type="number" readonly name="pr_total" id="pr_total" class="form-control" placeholder="قیمت کل">
                            </div></li>
                        <li class="pull-left" > <div class="form-group form-inline">

                                <input type="number"  name="pr_payment" id="pr_payment" class="form-control" placeholder="قیمت پرداختی">
                            </div></li>
                    </ul>
                </div>
            </div>
        </form><!--</form>-->
    </div>
</div> <!--</row>-->

<script type="text/javascript">

    $(document).ready(function () {

        selectTwo();

    });
    $(document).ready(function () {
        var rowcount,addBtn,tableBody;

        addBtn=$('#addNew');
        rowcount=$('#autocomplete_table tbody tr').length+1;
        tableBody=$('#autocomplete_table');
        function formHtml() {
            //rowcount=rowcount+1;
            console.log(rowcount);
            html='<tr id="row_'+rowcount+'">';
            html +='<td><button type="button" id="delete_'+rowcount+'"  class=" btn btn-danger btn-sm remove delete_row"><i class="glyphicon glyphicon-remove-sign"></i></button></td>';
            html +='<td><input type="text" data-field-name="product_name" name="product_name[]" id="product_name_'+rowcount+'" class=" form-control input-sm autocomplete_txt"><input type="hidden" data-field-name="product_id" name="product_id[]" id="product_id_'+rowcount+'" class=" form-control input-sm autocomplete_txt" autocomplete="off"><input type="hidden" data-field-name="product_store_id" name="product_store_id[]" id="product_store_id'+rowcount+'" class=" form-control input-sm autocomplete_txt" autocomplete="off"></td>';
            html +='<td><input type="text" data-field-name="product_code" name="product_code[]" id="product_code_'+rowcount+'" class="form-control input-sm autocomplete_txt"></td>';
            html +='<td><input type="text" data-field-name="quantity" name="product_quantity[]" id="product_quantity_'+rowcount+'" class="form-control input-sm  autocomplete_txt quantity amount"></td>';
            html +='<td><input type="text" data-field-name="product_sale_price" name="product_price[]" id="product_price_'+rowcount+'" class="form-control input-sm autocomplete_txt price"></td>';
            html +='<td><input type="text" data-field-name="product_buy_price" name="product_bprice[]" id="product_buy_'+rowcount+'" class="form-control input-sm autocomplete_txt price"></td>';
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


        function handleAutocomplete() {
            var fieldNam,currentEle;



            currentEle=$(this);
            fieldNam=currentEle.data('field-name');
            console.log(fieldNam);
            if(typeof fieldNam=='undefined'){
                return false;
            }
            currentEle.autocomplete({
                /* autofocus:true,
                 minLength:0,*/

                classes: {

                    "ui-autocomplete": "load",

                },

                source:function (data,cb) {




                    $.ajax({
                        url:'{{route('buy_product_search')}}',
                        method:'GET',
                        dataType:'json',
                        data:{
                            name:data.term,
                            fieldName:fieldNam,


                        },
                        success:function (res) {


                            var result;

                            result =[
                                {
                                    label:'there is no matching record for'+data.term,
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
                    /*$('#product_store_id_'+elementId).val(data.product_store_id);*/
                    /* $('#unit_id_'+elementId).val(data.unit_id);*/
                    $('#product_buy_'+elementId).val(data.product_buy_price);
                    $('#product_code_'+elementId).val(data.product_code);
                    $('#product_price_'+elementId).val(data.product_sale_price);
                    $('#product_store_id_'+elementId).val(data.product_stor_id);
                    $('#product_quantity_'+elementId).val(data.quantity);

                }
            });

        }


        function registerEvent() {
            addBtn.on('click',addNewRow);
            $(document).on('click','.delete_row',delete_Row);
            $(document).on('focus','.autocomplete_txt',handleAutocomplete);
            $('tbody').delegate('.amount','keyup',handleTotalRow);



        }
        registerEvent();

        /*Factor Dependency*/

    });

    $(document).ready(function () {
        $('.dynamic').change(function(){
            var buy_factor_id=$(this).val();
            var _token=$('input[name="_token"]').val();

            $.ajax({
                url: "{{route('buy_product.fetch')}}",
                method:'post',
                dataType:'json',
                data:{_token:_token,buy_factor_id:buy_factor_id},
                success:function (result) {

                    $.each(result,function(key, val){


                        if(key=='company_name'){

                            $('#company_name').val(val);
                        }else if(key=='store_name'){
                            $('#stack_name').val(val);

                        }else{

                            $('#factor_date').val(val);

                        }

                    })


                }


            })

        })
    })





</script>
