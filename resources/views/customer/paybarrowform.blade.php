<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>

<div class="col-lg-6 col-xs-12">
    <form method="post" id="frm" action="{{isset($customer) ?route('customerBarrow.update',$customer->sale_factor_id):route('customer.create')}}">
        {{isset($customer) ?method_field('put') :''}}
        {{csrf_field()}}
        <label>    مقدار کل  {{$customer->total_price}}     </label>
        <br>
<label>    مقدار پرداخت شده  {{$customer->recieption_price}}     </label>


        <div class="input-group  required margin-bottom-20" id="form-name-error">
            <div class="input-group-btn"><label for="pay_barrow" class="btn btn-default" id="pay_barrow">پرداخت بدهکاری :*</label></div>
            <!-- /.input-group-btn -->
            <input id="name" type="number" class="form-control required" placeholder="پرداخت بدهکاری" name="pay_barrow" autocomplete="off" value="{{$customer->recieption_price}}"  >
            <span id="name-error" class="help-block"></span>


        </div>

        <div class="input-group  margin-bottom-20" >
            <div class="input-group-btn"><label for="code"  id="code"></label></div>
            <!-- /.input-group-btn -->
            <input id="code" type="hidden" class="form-control "  name="code" autocomplete="off"  value="{{$customer->sale_factor_code}}" >



        </div>

        <div class="input-group  margin-bottom-20" >

            <div class="input-group-btn"><label for="total"  id="total"></label></div>
            <!-- /.input-group-btn -->
            <input id="total" type="hidden" class="form-control"name="total" autocomplete="off"  value="{{$customer->total_price}}" >



        </div>

        <div class="input-group  margin-bottom-20" >

            <div class="input-group-btn"><label for="payment"  id="payment"></label></div>
            <!-- /.input-group-btn -->
            <input id="payment" type="hidden" class="form-control "  name="payment" autocomplete="off"  value="{{$customer->recieption_price}}" >



        </div>

        <div class="input-group  margin-bottom-20" >

            <div class="input-group-btn"><label for="customer_id"  id="payment"></label></div>
            <!-- /.input-group-btn -->
            <input id="customer_id" type="hidden" class="form-control "  name="customer_id" autocomplete="off"  value="{{$customer->customer_id}}" >



        </div>








        <a href="javascript:ajaxLoad('{{route('customer.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save" style="margin-right: 5px;"></i></button>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        selectTwo();
        jalali();

    });

</script>
