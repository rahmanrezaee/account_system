<h4 >
    {{isset($panel_title) ?$panel_title :''}}
</h4>

<div class="col-lg-6 col-md-6 col-xs-12">
    <form method="post" id="frm"
          action="{{isset($customer) ?route('customer.update',$customer->customer_id):route('customer.create')}}">
        {{isset($customer) ?method_field('put') :''}}
        {{csrf_field()}}


        <div class="input-group  required margin-bottom-20 col-md-12" id="form-name-error">

            <label for="name" id="hid">نام :*</label>
            <!-- /.input-group-btn -->
            <input id="name" type="text" class="form-control required" placeholder="نام ::*" name="name"
                   autocomplete="off" value="{{old('name',isset($customer)?$customer->name:'')}}">
            <span id="name-error" class="help-block"></span>


        </div>

        <div class="form-group required" id="form-customer_code-error">
            <label for="customer_code">کد نمبر:*</label>
            <input type="text" class="form-control required" id="customer_code" name="customer_code" value="{{isset($customer)? $customer->customer_code:''}}"
                      placeholder="کد نمبر">
            <span id="customer_code-error" class="help-block"></span>
        </div>

        <div class="input-group  required margin-bottom-20 col-md-12" id="form-phone-error">

            <label for="phone">شماره مبایل:*</label>
            <!-- /.input-group-btn -->
            <input id="phone" type="text" class="form-control required" placeholder="شماره مبایل::*" name="phone"
                   autocomplete="off" value="{{old('phone',isset($customer)?$customer->	phone:'')}}">
            <span id="phone-error" class="help-block"></span>
        </div>

        <div class="form-group required" id="form-address-error">
            <label for="customer_address">آدرس:*</label>
            <textarea type="text" class="form-control required" id="address" name="address" value="" cols="4"
                      placeholder="آدرس">{{isset($customer)? $customer->address:''}}</textarea>
            <span id="address-error" class="help-block"></span>

        </div>



        @can('isManager')
            <div class="input-group margin-bottom-20" >
                <div class="input-group-btn"><label for="agency_id" class="btn btn-default">انتخاب نماینده گی:*</label></div>
                <!-- /.input-group-btn -->
                <select id="agency_id"  class="form-control select2_1" name="agency_id">
                    @foreach ($agencies as $agency)
                        <option value="{{ $agency->agency_id }}"  @if (isset($customer) && $agency->agency_id == $customer->agency_id)
                        selected
                                @endif>{{ $agency->agency_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan


        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save"
                                                                            style="margin-right: 5px;"></i></button>

        <a href="javascript:ajaxLoad('{{route('customer.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward"
                                                                                                 style="margin-right: 5px;"></i></a>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        selectTwo();
        jalali();

    });

</script>
