<div class="container col-md-6 col-sm-12" >
<div>
   <h4>{{isset($panel_title) ?$panel_title :''}}</h4>
</div>
<form method="post" id="frm" action="{{isset($decor) ? route('decoration.update',$decor->decor_id):route('decoration.createNew')}}" >
    {{isset($decor) ?method_field('put') :''}}
    {{csrf_field()}}
    <div style="margin-left: 20%; " >
    <div class=" form-group required " id="decoration_name"  >
        <label for="decoration_name" class="col-md-4 control-label">نام دیکوریشن</label>
        <input type="text" class="form-control " id="decoration_name" name="decoration_name" placeholder=""  required value="{{old('name',isset($decor)?$decor->name:'')}}">
        <span id="decoration_name-error" class="help-block"></span>
    </div>

    <div class="form-group" style="margin-bottom: 2%;">
        <div class="col-md-6 col-md-offset-4 register" >
            <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره</button>
            <a href="javascript:ajaxLoad('{{route('decoration.create')}}')" class="btn btn-danger glyphicon glyphicon-backward" data-dismiss="modal">لغو</a>
        </div>
    </div>
    </div>
</form>

</div>