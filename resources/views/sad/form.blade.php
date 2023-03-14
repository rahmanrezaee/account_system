<div class="container" >
    <div class="col-sm-12 col-md-12 col-lg-12">

        <h3 style="margin-right: 2%">
            {{isset($panel_title) ?$panel_title :''}}
        </h3>

            <form method="post" id="frm" action="{{isset($expense) ?url('expense/update/'.$expense->expense_id):route('expense.create')}}" >
                {{isset($expense) ?method_field('put') :''}}
                {{csrf_field()}}
                <div class="row">
                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-title-error">
                    <label for="title" class=" control-label">عنوان مصرف:</label>

                    <div >
                        <select name="title" id="title" class="form-control required">
                            @foreach($reasons as $reason)
                                <option value="{{ isset($reason)?$reason->expense_reason_id:''}} "
                                        {{isset($expense->expense_reason_id) && $reason->expense_reason_id == $expense->expense_reason_id  ?'selected'.'='.'selected':''}}>{{$reason->title}}</option>
                            @endforeach
                        </select>
                        <span id="title-error" class="help-block"></span>
                    </div>
                </div>

                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-amount-error">
                    <label for="amount" class=" control-label">مقدار مصرف شده:</label>

                    <div class="col-md-12">
                        <input id="amount" type="number" class="form-control required" name="amount"
                               value="{{ old('amount',isset($expense->amount)? $expense->amount:'') }}"
                        >
                        <span id="amount-error" class="help-block"></span>
                    </div>
                </div>

                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-currency-error">
                    <label for="currency" class=" control-label">پول رایج:</label>

                    <div class="col-md-12 col-sm-12">
                        <select id="currency" type="text" class="form-control required" name="currency">
                            <option  value="افغانی">افغانی</option>
                        </select>
                        <span id="currency-error" class="help-block"></span>
                    </div>
                </div>
                </div>

                <div class="row">
                <div class="form-groupcol-sm-4 col-md-4 col-lg-4 required" id="form-pay_date-error">
                    <label for="pay_date" class="control-label ">تاریخ مصرف:*</label>
                    <div class="input-group">
                        <input type="text" class="form-control required" placeholder="روز/ماه/سال" id="jalali-datepicker" value="{{old('pay_date',isset($expense->pay_date)? $expense->pay_date:'')}}"
                               name="pay_date" >
                        <span class="input-group-addon bg-primary text-white"><i class="fa fa-calendar"></i></span>
                    </div>
                    <span id="pay_date-error" class="help-block"></span>
                </div>
                <div class="form-group required col-sm-4 col-md-4 col-lg-4" id="form-employee-error">
                    <label for="employee" class=" control-label">کارمندان:</label>
                    <div >
                        <select name="employee" id="employee" class="form-control required">
                            @foreach($employees as $employee)
                                <option value="{{$employee->employee_id}}"
                                        {{isset($expense->employee_id) && $employee->employee_id == $expense->employee_id  ?'selected'.'='.'selected':''}} > {{$employee->first_name}}{{$employee->last_name}}</option>
                            @endforeach
                        </select>
                        <span id="employee-error" class="help-block"></span>
                    </div>
                </div>

                    <div class="form-group required col-sm-4 col-md-4 col-lg-4 " id="form-description-error">
                        <label for="description" class=" control-label"> توضیحات:</label>

                        <div class="col-md-12 col-sm-12">
                            <textarea id="description" type="text" class="form-control required" name="description" rows="2"
                                   value="{{ old('description',isset($expense->description)? $expense->description:'') }}"
                                      autofocus> </textarea>
                            <span id="description-error" class="help-block"></span>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-4 col-md-4 col-lg-4" >
                    <div class="col-md-12 col-sm-12 col-md-offset-4 register" >
                        <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره</button>

                        <a href="javascript:ajaxLoad('expense')" class="btn btn-danger glyphicon glyphicon-backward">لغو</a>
                    </div>
                </div>
                </div>
            </form>

    </div>
</div>
<script type="text/javascript">


    $(document).ready(function () {
        /*================
   JALALI DATEPICKER
   * ===============*/
        var opt = {

            // placeholder text

            placeholder: "",

            // enable 2 digits

            twodigit: true,

            // close calendar after select

            closeAfterSelect: true,

            // nexy / prev buttons

            nextButtonIcon: "fa fa-forward",

            previousButtonIcon: "fa fa-backward",

            // color of buttons

            buttonsColor: "پیشفرض ",

            // force Farsi digits

            forceFarsiDigits: true,

            // highlight today

            markToday: true,

            // highlight holidays

            markHolidays: false,

            // highlight user selected day

            highlightSelectedDay: true,

            // true or false

            sync: false,

            // display goto today button

            gotoToday: true

        }

        kamaDatepicker('jalali-datepicker', opt);

        /*================
		  EDTITABEL TABLE
		* ===============*/
        $('.select2_1').select2();

    });
</script>