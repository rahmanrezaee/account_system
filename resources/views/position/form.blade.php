<h4 style="margin-right: 3%">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-lg-6 col-md-6 col-xs-12">
    <form method="post" id="frm" action="{{isset($position) ?route('position.update',$position->position_id):route('position.create')}}" >
        {{isset($position) ?method_field('put') :''}}
        {{csrf_field()}}
        <div class="input-group   required  col-md-12    margin-bottom-20" id="form-position_name-error">
            <label for="position_name" >مقام یاوظیفه:* </label>
            <!-- /.input-group-btn -->

            <input  type="text" class="form-control  required" id="position_name" name="position_name" value="{{old('position_name',isset($position) ?$position->position_name :'')}}" placeholder="مقام یا وظیفه">

            <span id="position_name-error" class="help-block"></span>
        </div>

        <a href="javascript:ajaxLoad('{{route('position.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save" style="margin-right: 5px;"></i></button>
    </form>
</div>
