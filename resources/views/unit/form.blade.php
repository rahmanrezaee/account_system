<h4 style="margin-right: 3%">
    {{isset($panel_title) ?$panel_title :''}}
</h4>

<div class="col-lg-6 col-md-6 col-xs-12">
    <form action="{{isset($unit)?route('unit.update',$unit->unit_id):route('unit.create')}}" method="post" id="frm">
        {{isset($unit)?method_field('put'):''}}
        {{csrf_field()}}
        <div class="input-group  required  margin-bottom-20 col-md-12" id="form-unit_name-error">
            <label for="unit_name">واحد:*</label>
            <!-- /.input-group-btn -->
            <input  type="text" class="form-control required" id="unit_name" name="unit_name" value="{{old('unit_name',isset($unit) ?$unit->unit_name :'')}}" placeholder="واحد">
            <span  style="font-size: 25px;" id="unit_name-error" class="help-block"></span>
        </div>

        <div class="input-group  required  margin-bottom-20 col-md-12" id="form-unit_number-error">
            <label for="unit_number">تعداد:*</label>

            <!-- /.input-group-btn -->
            <input  type="text" class="form-control required" id="unit_number" name="unit_number" value="{{old('unit_number',isset($unit) ?$unit->unit_quantity :'1')}}" placeholder="تعداد">
            <span style="font-size: 25px;" id="unit_number-error" class="help-block"></span>
        </div>

        <a href="javascript:ajaxLoad('{{route('unit.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save" style="margin-right: 5px;"></i></button>
    </form>
</div>
