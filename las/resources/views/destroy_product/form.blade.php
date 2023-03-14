<div class="row">

</div>
<div class="row">
    <div class="col-md-12">
        <form action="{{route('destroy_product.create')}}" method="post" id="frm">
            {{csrf_field()}}
            <div class="form-row">
                <div class="form-group col-md-3">


                    <label for="stack_name">انتخاب گدام:*</label>
                    <select  name="stack_name" id="stack_name" class="form-control select2_1 dynamic" data-state="company">
                        <option value="" disabled="true" selected="true">لطفاگدام را انتخاب نمایید </option>
                        @foreach($stores as $store)
                            <option value="{{$store->store_id}}">{{$store->store_name}}</option>
                            @endforeach



                    </select>
                </div>
                <!--date -->
                <div class="form-group col-md-3">
                    <label for="pr_date" class="control-label ">تاریخ:*</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="تاریخ/روز/سال" id="jalali-datepicker" name="pr_date" autocomplete="off" >
                        <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                    </div>
                    <!-- /.input-group -->
                </div>
            </div><!--end row-->
            <div class="form-row">

                <div class="col-md-12 table-responsive">
                    <h5>اضافه نمودن معلومات بیشتر</h5>
                    <hr style="margin-top: 0px;">
                    <table class="table table-bordered table-striped " style="margin-bottom: 8px;" >
                        <thead>
                        <tr>
                            <th>حذف</th>
                            <th>نام محصول</th>
                            <th>تعداد</th>
                            <th>قیمت</th>
                            <th>مجموع کل</th>



                        </tr>
                        </thead>

                        <tbody id="tbody">
                        <tr>
                            <td><button class="del glyphicon glyphicon-remove-circle btn btn-danger btn-sm" type="button"></button></td>
                            <td><input type="text" name="product_name[]" id="product_name_1" data-field-name="product_name"  class="form-control input-sm auto_txt">
                                <input type="hidden" name="product_id[]" id="product_id_1" class=" auto_txt">
                                <input type="hidden" name="product_store_id[]" id="product_store_id_1" class="form-control input-sm  auto_txt"><input type="hidden" name="product_unit_quantity[]" id="product_unit_quantity_1" class="form-control input-sm  auto_txt"></td>
                            <td><input type="number" name="product_quantity[]" id="product_quantity_1" class="form-control input-sm  auto_txt quantity amount"></td>
                            <td><input type="number" name="package_price[]" id="package_price_1" class="form-control input-sm  auto_txt price amount"></td>
                            <td><input type="number" name="total_price[]" id="total_price_1" class="form-control input-sm  auto_txt total_amount"></td>

                        </tr>


                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="border: none;">
                                <button type="button" class=" btn btn-primary btn-sm addNew " title="اضافه نمودن سطر جدید"
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
                        <li><a href="javascript:ajaxLoad('{{route('destroy_product.list')}}')" class="btn  btn-default btn-sm ">
                                <span style="margin-right: 4px;">منصرف شدن</span></a></li>
                        <li class="pull-left"><div class="form-group form-inline ">

                                <input type="number" readonly name="pr_total" id="pr_total" class="form-control total" placeholder="قیمت کل">
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

var stack_id=0;
    function addNewRow() {
        var i=$('#tbody tr').length+1;
        $('#addNew').on('click',function () {
            var row='<tr>';
            row+='<td><button class="del glyphicon glyphicon-remove-circle btn btn-danger btn-sm" type="button"></button></td>';
            row+='<td><input type="text" name="product_name[]" id="product_name_'+i+'" data-field-name="product_name"  class="form-control input-sm auto_txt" autocomplete="off">\n' +
                '                                <input type="hidden" name="product_id[]" id="product_id_'+i+'" class="form-control input-sm  auto_txt">\n' +
                '                                <input type="hidden" name="product_store_id[]" id="product_store_id_'+i+'" class="form-control input-sm  auto_txt"><input type="hidden" name="product_unit_quantity[]" id="product_unit_quantity_'+i+'" class="form-control input-sm  auto_txt"></td>';
            row+='<td><input type="number" name="product_quantity[]" id="product_quantity_'+i+'" class="form-control input-sm  auto_txt quantity amount"></td>';
            row+='<td><input type="number" name="package_price[]" id="package_price_'+i+'" class="form-control input-sm   price amount"></td>';
            row+='<td><input type="number" name="total_price[]" id="total_price_'+i+'" class="form-control input-sm  auto_txt total_amount"></td></tr>';

            $('#tbody').append(row);
            i++;
        })
    }

    function deleteRow() {
        $(document).on('click','.del',function () {
            if($('#tbody tr').length===1)
                alert('شما نمی توانید این ریکورد را حذف کنید!');
            else{
                $(this).parents('tr').remove();
            }
        })
    }


    function destroyProductSearch() {

        $(document).on('click','.auto_txt',function () {

                var query=$(this).data('field-name');
                if(typeof query=='undefined')

                    return false;
            $(this).autocomplete({

                source:function (request,response) {
                    $.ajax({
                        url:'{{route('destroy_product.search')}}',
                        dataType:'json',
                        data:{
                            name:request.term,
                            query:query,
                            stack_id:stack_id,
                        },
                        success:function (data) {
                            var result;
                            result={
                               label:'no such data'+request.term,
                               value:'',
                            };
                            if(data.length){

                                result=$.map(data,function (item) {


                                    return {
                                        label:item[query],
                                        value:item[query],
                                        data:item
                                    };
                                })

                            }
                            response(result);
                        },
                        error:function (xhr) {
                            alert(xhr.responseText);
                        }
                    })

                },
                select:function (event,ui) {
                var total=0;
                    var data=ui.item.data;

                    id_arr=$(this).attr('id');
                    id=id_arr.split('_');
                    currentId=id[id.length-1];

                    $('#product_name_'+currentId).val(data.product_name);
                    $('#product_id_'+currentId).val(data.product_id);
                    $('#product_store_id_'+currentId).val(data.product_stor_id);
             $('#product_quantity_'+currentId).val(data.quantity);
              $('#product_price_'+currentId).val();
                    $('#product_unit_quantity_'+currentId).val(data.unit_quantity);


                }

            })






        })

    }
    function totalRow() {
        $(document).on('keyup','.amount',function () {
            var amount_total=0;
            var tr =$(this).parents('tr');
            var qty=tr.find('.quantity').val();
            var price=tr.find('.price').val();
           var total=qty*price;
           tr.find('.total_amount').val(total);
           $('.total_amount').each(function (i,e) {
              var amount=$(this).val()-0;
              amount_total+=amount;


           });

            $('.total').val(amount_total);

        })
    }


       $(document).on('change','.dynamic',function () {

            stack_id=$(this).val();



        });





    $(document).ready(function () {
        addNewRow();
        deleteRow();
        destroyProductSearch();
        totalRow();
        selectTwo();
        jalali();





  })

</script>
