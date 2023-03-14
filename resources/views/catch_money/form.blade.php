
<div class="col-md-12 col-lg-12 col-md-offset-3 margin-top-20">
    <h4 class="box-title">
        {{isset($panel_title) ?$panel_title :''}}
    </h4>

    <form method="post" id="frm" action="{{  isset($catchMoney)? route('catch_money.update',[$catchMoney->id]) :route('catch_money.create') }}">
        {{csrf_field()}}

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group required" id="form-catch_amount-error">
                    <label for="catch_amount"> مقدار برداشت*</label>
                    <input type="text" name="catch_amount" id="catch_amount" class="form-control required"
                           value="{{old('catch_amount',isset($catchMoney)?$catchMoney->amount:'')}}" >
                    <span id="catch_amount-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group required" id="form-account_id-error">
                    <label for="account_id">نوعیت حساب </label>
                    <select name="account_id" id="account_id" class="form-control ">
                        @foreach ($moneyStores as $moneyStore)
                            <option value="{{ $moneyStore->store_id }}" @if (isset($catchMoney) && $moneyStore->store_id == $catchMoney->account_type)
                            selected
                                    @endif>{{ $moneyStore->name }}</option>
                        @endforeach
                    </select>
                    <span id="account_id-error" class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group  required" id="form-catch_date-error">
                    <label for="catch_date" class="control-label ">تاریخ برداشت:*</label>
                        <input type="date" class="form-control  required" placeholder="روز/ماه/سال"
                              name="catch_date"
                               value="{{ old('catch_money',isset($catchMoney)?$catchMoney->date:date("Y-m-d")) }}">

                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group required" id="form-employee_id-error">
                    <label for="employee_id">کارمند مورد نظر</label>
                    <select name="employee_id" id="employee_id" class="form-control ">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->employee_id }}" @if (isset($catchMoney) && $employee->employee_id == $catchMoney->employee_id)
                            selected
                                    @endif>{{ $employee->first_name }} &nbsp;{{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                    <span id="employee_id-error" class="help-block"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group required" id="form-payment_status-error">
                    <label for="payment_status">انتخاب برداشت یا پرداخت</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="draw" @if(isset($catchMoney))  {{ $catchMoney->payment_status == 'draw' ? "selected" : "" }} @endif>برداشت</option>
                        <option value="pay " @if(isset($catchMoney))  {{ $catchMoney->payment_status == 'pay' ? "selected" : "" }} @endif>پرداخت</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group required" id="form-catch_money_description-error">
                    <label for="catch_money_description">توضیحات:*</label>
                    <textarea type="text" class="form-control required" id="catch_money_description" name="catch_money_description"
                              value="" cols="4"
                              placeholder="توضیحات">{{ isset($catchMoney)?$catchMoney->description:'' }}</textarea>
                    <span id="catch_money_description-error" class="help-block"></span>

                </div>
            </div>
        </div>

        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('catch_money.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        </div>
    </form>


</div>

<script type="text/javascript">

    $(document).ready(function () {
        $(".date-picker").persianDatepicker({

        });
        $('.select2_1').select2();

    });

</script>

