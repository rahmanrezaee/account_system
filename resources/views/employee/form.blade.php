<div class="container">
    <h3 >
        {{isset($panel_title) ?$panel_title :''}}
    </h3>
    <div class="row col-lg-10 col-md-10">
        <form method="post" id="frm" enctype="multipart/form-data"
              action="{{isset($employee) ? route('employee.update',$employee->employee_id ) : route('employee.create')}}">
            {{isset($employee) ? method_field('put') :''}}
            {{csrf_field()}}
            <div class="col-md-8">
                <div class="form-group required" id="form-name-error">
                    <label for="name" class="col-md-4 control-label">نام : </label>
                    <input id="name" type="text" class="form-control required" name="name"
                           value="{{ old('name',isset($employee)? $employee->first_name:'') }}"
                           autofocus>
                    <span id="name-error" class="help-block"></span>
                </div>
                <div class="form-group required " id="form-last_name-error">
                    <label for="version" class="col-md-4 control-label"> تخلص :</label>
                    <input id="version" type="text" class="form-control required" name="last_name"
                           value="{{ old('last_name',isset($employee)? $employee->last_name:'') }}"
                           autofocus>
                    <span id="last_name-error" class="help-block"></span>
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

                <div class="form-group required" id="form-position-error">
                    <label for="version" class="col-md-4 control-label"> وظیفه :</label>
                    <select name="position" id="position" class="form-control required">
                        @foreach($position as $po)
                            <option
                                    @if(isset($employee))
                                    @if($po->position_id == $employee->position_id)
                                    selected
                                    @endif
                                    @endif

                                    value="{{ $po->position_id }}">{{ $po->position_name }}
                            </option>
                        @endforeach
                    </select>
                    <span id="position-error" class="help-block"></span>
                </div>


                <div class="form-group required " id="form-email-error">
                    <label for="email" class="col-md-4 control-label">ایمیل :</label>
                    <input id="email" type="email" class="form-control " name="email"
                           value="{{ old('email',isset($employee)? $employee->email:'') }}"
                           autofocus>
                    <span id="email-error" class="help-block"></span>
                </div>


                <div class="form-group required" id="form-phone-error">
                    <label for="phone" class="col-md-4 control-label">نمبرتماس :</label>
                    <input id="phone" type="tel" class="form-control required" name="phone"
                           value="{{ old('phone',isset($employee)? $employee->phone:'') }}"
                           autofocus>
                    <span id="phone-error" class="help-block"></span>
                </div>
                <div class="form-group required " id="form-address-error">
                    <label for="address" class="col-md-4 control-label">آدرس :</label>
                    <input id="address" type="text" class="form-control required" name="address"
                           value="{{ old('address',isset($employee)? $employee->address:'') }}"
                           autofocus>
                    <span id="address-error" class="help-block"></span>
                </div>



                <div class="form-group required" id="form-salary-error">
                    <label for="salary">مبلغ معاش :*</label>

                    <div class="input-group d-flex">
                        <input type="text"
                               value="{{ old('salary',isset($employee)? $employee->salary:'') }}"
                               id="salary" class="form-control required" name="salary">

                        <select class="form-control no-padding" name="currency_id" id="currency_id">
                            @foreach($currencies as $currency)
                                <option
                                       @if (isset($employee))
                                       @if(  $employee->currency_id == $currency->currency_id) selected
                                       @endif
                                       @endif
                                        value="{{ $currency->currency_id }}">{{ $currency->currency_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group required" id="form-shift-error">
                    <label for="shift" class="col-md-4 control-label required">تایم کاری :</label>
                    <select name="shift" id="shift" class="form-control required">
                        <option value="تمام وقت"> تمام وقت</option>
                        <option value="قبل ازظهر">قبل ازظهر</option>
                        <option value="بعدازظهر">بعدازظهر</option>
                    </select>
                    <span id="shift-error" class="help-block"></span>
                </div>
                <div class="form-group required" id="form-come_date-error">
                    <label for="come_date" class="col-md-4 control-label">تاریخ استخدام :</label>
                    <input placeholder="روز/ماه/سال" type="date" class="form-control  required" name="come_date"
                           value="{{ old('come_date',isset($employee)? $employee->hire_date:date("Y-m-d")) }}"
                           autofocus>
                    <span id="come_date-error" class="help-block"></span>
                </div>
                <div class="form-group " >
                    <label for="gender" class="col-md-4 control-label">جنسیت : </label>
                    <label>
                        <input type="radio"

                               @if(isset($employee))

                               @if($employee->gender == "آقا")
                               checked
                               @endif
                               @endif
                               name="gender" value="آقا" checked/>
                        آقای
                    </label>
                    <label>
                        <input type="radio"
                               @if(isset($employee))

                               @if($employee->gender == "خانم")
                               checked
                               @endif

                               @endif
                               name="gender" value="خانم"/>
                        خانم
                    </label>
                </div>
                <div class="form-group">
                    <label for="status" class="col-md-4 control-label">حالت مدنی: </label>
                    <label>
                        <input type="radio"
                               @if(isset($employee))

                               @if($employee->marital_status == "مجرد")
                               checked
                               @endif

                               @endif name="status" value="مجرد" checked/>
                        مجرد
                    </label>
                    <label>
                        <input type="radio"
                               @if(isset($employee))

                               @if($employee->marital_status == "متاهل")
                               checked
                               @endif

                               @endif
                               name="status" value="متاهل"/>
                        متاهل
                    </label>

                </div>

            </div>
            <div class="col-md-3" style="margin-top: 28px;">
                <div class="form-group">
                    <input name="photo"
                           {{ isset($employee)? 'disabled' : " " }}  style='height: 0px;width:0px; overflow:hidden;'
                           id="photo" type='file'
                           onchange="readURL1(this);"/>
                    <img id="blah1" style="height: 100%;width: 100% "
                         src="{{ isset($employee) ? asset($employee->photo) : asset('image/empty_profile.jpg') }}"
                         alt="your image"/>
                    <label for="photo" {{ isset($employee)? 'disabled' : " " }} class="form-control btn btn-default">انتخاب
                        عکس :</label>
                </div>
                <div class="form-group">
                    <input name="agreepaper" style='height: 0px;width:0px; overflow:hidden;'
                           {{ isset($employee)? 'disabled' : " " }} id="agreement" type='file'
                           onchange="readURL2(this);"/>
                    <img id="blah2" style="height: 100%;width: 100%"
                         src="{{isset($employee) ? asset( $employee->agreement_paper ) :  asset('image/agreement.jpg') }}"
                         alt="your image"/>
                    <label for="agreement"
                           {{ isset($employee)? 'disabled' : " " }} class="form-control btn btn-default">انتخاب تعهد
                        نامه :</label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6 col-md-offset-4 register">
                    <a href="javascript:ajaxLoad('{{ route('employee.list') }}')" class="btn btn-danger">لغو</a>
                    <button type="submit" id="btn_save" class="btn btn-primary"> ذخیره</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">


    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });

        /*================
		  EDTITABEL TABLE
		* ===============*/
        $('.select2_1').select2();

    });
</script>
