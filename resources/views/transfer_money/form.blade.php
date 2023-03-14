<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-12 col-lg-12 col-md-offset-3">
    <form method="post" id="frm" action="{{ isset($transferMoney)? route('money_transfer.update',[$transferMoney->transfer_id]): route('money_transfer.create') }}">
        {{csrf_field()}}
        <div class="row">
            @if (session('errors'))
                <div class="alert alert-danger">
                    {{ session('errors') }}
                </div>
            @endif
            <div class="col-md-6 col-lg-6">
                <div class="form-group required" id="form-sender_account_id-error">
                    <label for="sender_account_id" ><span id="s_currency"></span>نوعیت حساب فرستنده</label>
                    <select name="sender_account_id" id="sender_account_id" class="form-control" onchange="sender_currency()">
                        @foreach ($moneyStores as $moneyStore)
                            @if (Auth::user()->user_level == 1  && Auth::user()->agency_id == 0)
                                <p id="sender_currency" style="display: none">نوع پول در این حساب می {{ $moneyStore->currency['currency_name'] }} باشد </p>
                                <option value="{{ $moneyStore->store_id }}" @if (isset($transferMoney) && $moneyStore->store_id == $transferMoney->sender_id)
                                selected
                                        @endif>{{ $moneyStore->name }}&nbsp;&nbsp;<span id="sender_currency">({{ $moneyStore->currency['currency_name'] }})</span></option>

                            @elseif (Auth::user()->agency_id == $moneyStore->agency_id)
                                <p id="sender_currency" style="display: none">نوع پول در این حساب می {{ $moneyStore->currency['currency_name'] }} باشد </p>
                                <option value="{{ $moneyStore->store_id }}" @if (isset($transferMoney) && $moneyStore->store_id == $transferMoney->sender_id)
                                selected
                                        @endif>{{ $moneyStore->name }}&nbsp;&nbsp;<span id="sender_currency">({{ $moneyStore->currency['currency_name'] }})</span></option>
                            @endif
                        @endforeach
                    </select>
                    <span id="sender_account_id-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group required" id="form-receiver_account_id-error">
                    <label for="receiver_account_id" id="conf"> نوعیت حساب گیرنده <span id="receiver_currency"> </span></label>
                    <select name="receiver_account_id" id="receiver_account_id" onload="myFunction()" onmouseout="myFunction()" class="form-control" onkeyup="check_conf()">
                        @foreach ($moneyStores as $moneyStore)
                            <option value="{{ $moneyStore->store_id }}" @if (isset($transferMoney) && ( $moneyStore->store_id == $transferMoney->receiver_id))
                                selected
                                @endif>{{ $moneyStore->name  }}&nbsp;&nbsp;<span id="receiver_currency">({{ $moneyStore->currency['currency_name'] }})</span></option>
                        @endforeach
                    </select>
                    <span id="receiver_account_id-error" class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-lg-4">
                <div class="form-group required" id="form-payment_amount-error">
                    <label for="payment_amount"> :مقدار پرداختی *</label>
                    <input type="text" step="any" name="payment_amount" id="payment_amount" class="form-control required"
                           value="{{old('payment_amount' ,isset($transferMoney)?$transferMoney->payment_amount:'')}}" >
                    <span id="payment_amount-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-4 col-lg-4">
                <div class="form-group required" id="form-rate-error">
                    <label for="rate"> :نرخ *</label>
                    <input type="text" step="any" name="rate" id="rate" class="form-control required"
                           value="{{old('rate',isset($transferMoney)?$transferMoney->rate:'')}}">
                    <span id="rate-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-4 col-lg-4">
                <!--date -->
                <div class="form-group " id="form-pr_date-error">
                    <label for="pr_date" class="control-label ">تاریخ:*</label>
                        <input type="date" class="form-control" placeholder="تاریخ/روز/سال" name="pr_date" autocomplete="off" value="{{old('pr_date',isset($transferMoney)?$transferMoney->date:date("Y-m-d"))}}" >
                    <!-- /.input-group -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="form-group required" id="form-transfer_description-error">
                    <label for="transfer_description">توضیحات:*</label>
                    <textarea type="text" class="form-control required" id="transfer_description" name="transfer_description"
                              value="" cols="4"
                              placeholder="توضیحات">{{isset($transferMoney)?$transferMoney->description:'' }}</textarea>
                    <span id="transfer_description-error" class="help-block"></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                اطلاعات <i class="fa fa-save" ></i>
            </button>

            <a href="javascript:ajaxLoad('{{route('money_transfer.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>

        </div>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });
        $('.select2_1').select2();
    });

    function myFunction() {
        var s = document.getElementById("sender_account_id").value;
        var r = document.getElementById("receiver_account_id").value;

        if (s === r){
         document.getElementById('conf').style.color = 'red';
        } else {
            document.getElementById('conf').style.color = 'green';
        }
    }

</script>

