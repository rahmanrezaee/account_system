<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-lg-8 col-md-10 col-xs-12">
    <label>مقدار بدهی از فکتور {{$factorPayment->factor_code}}
        مبلغ :
        {{$factorPayment->total_payment- $factorPayment->current_payment}}
    میباشد
    </label>
    <form method="post" id="frm"
          action="{{isset($factorPayment) ?route('buy_factor_payment',$factorPayment->buy_factor_id):route('category.create')}}">
        {{isset($factorPayment) ?method_field('put') :''}}
        {{csrf_field()}}

        <div class="input-group   required  margin-bottom-20" id="form-payment_amount-error">
            <label for="payment_amount">مقدار قابل پرداخت:*</label>
            <!-- /.input-group-btn -->
            <input type="text" class="form-control required col-md-6" id="payment_amount" name="payment_amount"
                   value="{{old('payment_amount',isset($factorPayment) ?($factorPayment->total_payment - $factorPayment->current_payment) :'')}}"
                   placeholder="مقدار بدهی :*">
            <span style="font-size: 25px;" id="payment_amount-error" class="help-block"></span>
        </div>

        <div>
            <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save"
                                                                                style="margin-right: 5px;"></i></button>
            <a href="javascript:ajaxLoad('{{route('buy_factor.report')}}')" class="btn btn-danger">لغو<i
                        class="fa fa-backward"
                        style="margin-right: 5px;"></i></a>

        </div>
    </form>
</div>
