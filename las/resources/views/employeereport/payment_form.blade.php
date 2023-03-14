<div class=" col-md-6 col-lg-6 col-xs-12" >

    <h3 style="margin-right: 2%">
        {{isset($panel_title) ?$panel_title :''}}
    </h3>

    <form method="post" id="frm" action="{{isset($salary) ?route('employeereport.payment',$salary->payment_id):route('employeereport.create')}}">
        {{isset($salary) ?method_field('put') :''}}
        {{csrf_field()}}

        @if(isset($salary))

            <div class="input-group margin-bottom-20 required" id="form-employee_id-error" >
                <div class="input-group-btn"><label for="employee_id" class="btn btn-default">مشخصات کار مند</label></div>
                <!-- /.input-group-btn -->

                <input id="employee_id" type="text" class="form-control" placeholder=" معاش" name="employee_id" disabled="disabled"
                       value="{{$employee->first_name}}&nbsp;{{$employee->last_name}}" >
                <input id="employee_id" type="hidden" class="form-control" placeholder=" معاش" name="employee_id" disabled="disabled"
                       value="{{$employee->employee_id}}">
                <span id="employee_id-error" class="help-block"></span>
            </div>





            <!-- /.input-group -->
            <div class="input-group margin-bottom-20 required" id="form-salary-error">

                <div class="input-group-btn"><label for="salary" class="btn btn-default">معاش:</label></div>

                <div class="input-group d-flex">
                    <input type="text"
                           value="{{old('salary',isset($employee->salary)? $employee->salary:'')}}"
                           required name="salary"
                           id="salary"
                           class="form-control select2_1 salary col-md-6">

                    <select class="form-control no-padding col-md-6" readonly="" name="currency_id" id="currency_id">
                        <option value="{{ $employee->currency_id }}">{{ getCurrency($employee->currency_id) }}</option>
                    </select>
                </div>
            </div>

            <div class="input-group margin-bottom-20 required" id="form-payment_amount-error">
                <div class="input-group-btn"><label for="payment_amount" class="btn btn-default">مقدار پرداخت:*</label></div>
                <!-- /.input-group-btn -->
                <input id="payment_amount" type="number" class="form-control required" placeholder=" مقدار پرداخت " name="payment_amount"
                       value="{{old('payment_amount',isset($salary->payment_amount)?  $salary->payment_borrow:'')}}">
                <span id="payment_amount-error" class="help-block"></span>
            </div>

            <div class="input-group margin-bottom-20 required" id="form-account_id-error">
                <div class="input-group-btn"><label for="account_id" class="btn btn-default">انتخاب صندوق</label></div>

                <select name="account_id" id="account_id" class="form-control select2_1 required ">
                <option value="-1">صندق تان را انتخاب کنید</option>
                    @foreach ($moneyStores as $moneyStore)
                        <option currency_name="{{ getCurrency($moneyStore->currency_id) }}" value="{{ $moneyStore->store_id }}">
                            {{ $moneyStore->name }}
                        </option>
                    @endforeach
                </select>
                <span id="account_id-error" class="help-block"></span>
            </div>

            <div class="input-group margin-bottom-20 required">
                <div class="input-group-btn"><label for="currency_rate" class="btn btn-default">نرخ ارز و ارز صندق:</label></div>

                <div class="input-group d-flex">
                    <input type="text"
                           value="{{ old('currency_rate',isset($expense->currency_rate)? $expense->currency_rate:'1') }}"
                           required name="currency_rate"
                           id="currency_rate"
                           class="form-control select2_1 sale_factor_code col-md-6">

                    <select class="form-control no-padding col-md-6" readonly="" name="currency_store_id" id="currency_store_id">

                    </select>
                </div>
            </div>



            <!-- /.input-group -->
            <div class="input-group margin-bottom-20 required" id="form-payment_month-error">
                <div class="input-group-btn"><label for="payment_month" class="btn btn-default">طلب ازماه</label></div>
                <!-- /.input-group-btn -->
                <input name="payment_month" id="payment_month" class="form-control required "
                       value="{{old('payment_month',isset($salary->payment_month)?  $salary->payment_month:'')}}">
                <span id="payment_month-error" class="help-block"></span>
            </div>

            <!-- /.input-group -->
            <div class="input-group margin-bottom-20 required" id="form-payment_date-error">
                <div class="input-group-btn"><label for="payment_date" class="btn btn-default">تاریخ:*</label></div>
                <input type="date" class="form-control   required" placeholder="روز/ماه/سال" name="payment_date"
                       value="{{old('payment_date',isset($salary->payment_date)? $salary->payment_date:date('Y-m-d'))}}">
                <span id="payment_date-error" class="help-block"></span>
            </div>

            <input id="id" type="hidden"  class="form-control" name="id"
                   value="{{ old('id',isset($salary->payment_id)? $salary->payment_id:'') }}"
                   autofocus>

            <!-- /.input-group -->
            <a href="javascript:ajaxLoad('{{ route('employeereport.list') }}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
            <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save" style="margin-right: 5px;"></i></button>
    </form>

</div>

<script type="text/javascript">
    $(document).ready(function () {

        selectTwo();
        jalali();

    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });
        $('.select2_1').select2();

    });



    $(document).ready(function () {




        $("#employee_id").change(function () {

            var search = $(this).val();

            let currency = $("#currency_id");

            $.ajax({
                type: "get",
                url: "employeereport/getSalary/" + search,
                contentType: false,
                success: function (data) {

                    $("#salary").val(data.sallary);
                    currency.html("<option value='"+data.currency_id+"'>" + data.currency_name + "</option>");

                },
            });

        });
        $("#account_id").change(function () {

            setCurrencyRate();
            let curr = $('#account_id option:selected').attr('currency_name');
            console.log(curr);

            $("#currency_store_id").html("<option >"+curr+"</option>")

        });

    })


    function setdata(data) {

        $("#employee_id").val(data);

    }

    function setCurrencyRate() {

        var _token = $('input[name="_token"]').val();

        let account = $("#account_id").val();
        let currency_id = $("#currency_id").val();



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

@endif
