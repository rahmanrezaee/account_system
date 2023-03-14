<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{ isset($agency)? route('agency.update',[$agency->agency_id]):route('agency.create') }}">
        {{csrf_field()}}

        <div class="form-group required" id="form-agency_name-error">
            <label for="agency_name"> اسم نماینده گی*</label>
            <input type="text" name="agency_name" id="agency_name" class="form-control required"
                   value="{{old('agency_name',isset($agency)?$agency->agency_name:'')}}" >
            <span id="agency_name-error" class="help-block"></span>
        </div>
        <div class="form-group required" id="form-location-error">
            <label for="location"> موقعیت نماینده گی*</label>
            <input type="text" name="location" id="location" class="form-control required"
                   value="{{old('location',isset($agency)?$agency->location:'')}}" >
            <span id="location-error" class="help-block"></span>
        </div>
        <div class="form-group required" id="form-address-error">
            <label for="address"> آدرس نماینده گی*</label>
            <input type="text" name="address" id="address" class="form-control required"
                   value="{{old('address',isset($agency)?$agency->address:'')}}" >
            <span id="address-error" class="help-block"></span>
        </div>
        <div>
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                 اطلاعات <i class="fa fa-save" ></i>
            </button>
            <a href="javascript:ajaxLoad('{{route('agency.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        </div>
    </form>


</div>

