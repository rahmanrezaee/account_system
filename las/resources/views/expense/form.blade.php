<div class="container">
    <div class="col-sm-12 col-md-12 col-lg-12">

        <h3 style="margin-right: 2%">
            {{isset($panel_title) ?$panel_title :''}}
        </h3>

        <form method="post" id="frm"
              action="{{isset($expense) ?url('expense/update/'.$expense->expense_id):route('expense.create')}}">
            {{isset($expense) ?method_field('put') :''}}
            {{csrf_field()}}
            <div class="row">
                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-title-error">
                    <label for="title" class=" control-label">عنوان مصرف:</label>

                    <div>
                        <select name="title" id="title" class="form-control required">
                            @foreach($reasons as $reason)
                                <option value="{{ isset($reason)?$reason->expense_reason_id:''}} "
                                        {{isset($expense->expense_reason_id) && $reason->expense_reason_id == $expense->expense_reason_id  ?'selected'.'='.'selected':''}}>{{$reason->title}}</option>
                            @endforeach
                        </select>
                        <span id="title-error" class="help-block"></span>
                    </div>
                </div>


                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-account-error">
                    <label for="account" class=" control-label">حساب مورد نظر :</label>
                    <div>
                        <select name="account" id="account" class="form-control required">
                            <option value="-1">حساب تان را انتخاب کنید</option>
                            @foreach($moneyStores as $moneyStore )
                                <option value="{{$moneyStore->store_id}}"
                                        {{isset($expense) && $moneyStore->store_id == $expense->account_id  ?'selected'.'='.'selected':''}} > {{$moneyStore->name}}</option>
                            @endforeach
                        </select>
                        <span id="account-error" class="help-block"></span>
                    </div>
                </div>

                <div class="form-group col-md-4 col-lg-4 col-sm-12 required">
                    <label for="stack_name">نرخ ارز و ارز:*</label>

                    <div class="input-group d-flex">

                        <input type="text"
                               value="{{ old('currency_rate',isset($expense->currency_rate)? $expense->currency_rate:'1') }}"
                               required name="currency_rate"
                               id="currency_rate"
                               class="form-control select2_1 sale_factor_code">

                        <select class="form-control no-padding" name="currency_id" id="currency_id">
                            @foreach($currencies as $currency)
                                <option
                                        @if( isset($expense->currency_rate) && $expense->currency_id == $currency->currency_id) selected
                                        @endif
                                        value="{{ $currency->currency_id }}">{{ $currency->currency_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-amount-error">
                    <label for="amount" class=" control-label">مقدار مصرف شده:</label>

                    <div class="">
                        <input id="amount" type="text" class="form-control required" name="amount"
                               value="{{ old('amount',isset($expense->amount)? $expense->amount:'') }}"
                        >
                        <span id="amount-error" class="help-block"></span>
                    </div>
                </div>

                <div class="form-group col-sm-4 col-md-4 col-lg-4 required" id="form-pay_date-error">
                    <label for="pay_date" class="control-label ">تاریخ مصرف:</label>
                   <input type="date" class="form-control required " placeholder="روز/ماه/سال"

                               value="{{old('pay_date',isset($expense->pay_date)? $expense->pay_date: date("Y-m-d"))}}"
                               name="pay_date">
                    <span id="pay_date-error" class="help-block"></span>
                </div>


            </div>
            <div class="row">
                <div class="form-group required" id="form-description-error">
                    <label for="description" class=" control-label"> توضیحات:</label>

                    <div class="col-md-12 col-sm-12">
                            <textarea id="description" type="text" class="form-control required" name="description"
                                      rows="2"
                                      value="{{ old('description',isset($expense->description)? $expense->description:'') }}"
                                      autofocus> </textarea>
                        <span id="description-error" class="help-block"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4 col-md-4 col-lg-4">
                    <div class="col-md-12 col-sm-12 col-md-offset-4 register">
                        <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk">
                            ذخیره
                        </button>

                        <a href="javascript:ajaxLoad('{{ route('expense.list') }}')"
                           class="btn btn-danger glyphicon glyphicon-backward">لغو</a>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<script type="text/javascript">


    $(document).ready(function () {
        $(".date-picker").persianDatepicker({

        });
        $('.select2_1').select2();

    });


    $(document).ready(function () {


        $('#currency_id').attr('disabled',true);
        $('#currency_rate').attr('disabled',true);
        
        
        if ($("input[name='_method']").val() == "put"){


            $('#currency_id').attr('disabled',false);
            $('#currency_rate').attr('disabled',false);
        }
        

        $("#account").change(function () {

            if($(this).val() != -1){

                $('#currency_id').attr('disabled',false);
                $('#currency_rate').attr('disabled',false);

            }else {
                $('#currency_id').attr('disabled',true);
                $('#currency_rate').attr('disabled',true);
            }

            setCurrencyRate($('#currency_id').val())


        });

        $('#currency_id').change(function () {

            setCurrencyRate($(this).val())
        });


    })

    function setCurrencyRate(currency_id) {

        var _token = $('input[name="_token"]').val();

        let account = $("#account").val();

        if (account == null) {
            alert("صندق تان را انتخاب کنید!!")
        } else {


            $.ajax({
                url: "{{route('sale.currencyExchangerByCurrency')}}",
                method: 'get',
                dataType: 'json',
                data: {_token: _token, currency_id: currency_id,account_id:account},
                success: function (result) {

                    $("#currency_rate").val(result)
                }


            });
        }

    }

</script>