<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{ isset($moneyStore)? route('money_exchange.update',[$moneyStore->store_id]): route('money_exchange.create') }}">
        {{csrf_field()}}


        <div class="form-group required" id="form-account_type-error">
            <label for="account_type"> :نوعیت حساب*</label>
            <input type="text" name="account_type" id="account_type" class="form-control required"
                   value="{{old('account_type',isset($moneyStore)?$moneyStore->name:'')}}" >
            <span id="account_type-error" class="help-block"></span>
        </div>

        @can('isManager')
            <div class="form-group required " id="form-agency_id-error">
                <label for="agency_id" class="col-md-4 control-label"> انتخاب نماینده گی :</label>
                <select name="agency_id" id="agency_id" class="form-control">
                    @foreach ($agencies as $agency)
                        <option value="{{ $agency->agency_id }}" @if (isset($employee) && $agency->agency_id == $employee->agency_id)
                        selected
                                @endif >{{ $agency->agency_name }}</option>
                    @endforeach
                </select>
                <span id="agency_id-error" class="help-block"></span>
            </div>
        @endcan

        <div class="form-group required" id="form-amount-error">
            <label for="amount"> :مقدار پول *</label>
            <input type="number" name="amount" step="any" id="amount" class="form-control required"
                   value="{{old('account_type',isset($moneyStore)?$moneyStore->money_amount:'')}}" >
            <span id="amount-error" class="help-block"></span>
        </div>
        <div class="form-group required" id="form-currency_id-error">
            <label for="currency_id">واحد پولی</label>
            <select name="currency_id" id="currency_id" class="form-control ">
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->currency_id }}" @if (isset($moneyStore) &&  $currency->currency_id == $moneyStore->currency_id)
                    selected
                            @endif>{{ $currency->currency_name }}</option>
                @endforeach
            </select>
            <span id="currency_id-error" class="help-block"></span>
        </div>
        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('money_exchange.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        </div>
    </form>


</div>

