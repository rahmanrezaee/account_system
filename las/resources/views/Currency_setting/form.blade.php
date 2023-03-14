<h4 class="box-title ">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{ isset($currency)? route('currency.update',[$currency->currency_id]):route('currency.create') }}">
        {{csrf_field()}}

        <div class="form-group required" id="form-currency_name-error">
            <label for="currency_name"> نام ارز*</label>
            <input type="text" name="currency_name" id="currency_name" class="form-control required"
                   value="{{old('currency_name',isset($currency)?$currency->currency_name:'')}}" >
            <span id="currency_name-error" class="help-block"></span>
        </div>

        <div class="form-group required" id="form-symbol-error">
            <label for="symbol">سمبول</label>
            <input type="text" name="symbol" id="" value="{{old('symbol',isset($currency) ? $currency->symbol:'')}}" class="form-control required">
            <span id="symbol-error" class="help-block"></span>
        </div>


        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('currency.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        </div>
    </form>

</div>