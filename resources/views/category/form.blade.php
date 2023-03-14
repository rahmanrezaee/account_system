<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>


<div class="col-lg-6 col-md-6 col-xs-12">
    <form method="post" id="frm"
          action="{{isset($Category) ?route('category.update',$Category->category_id):route('category.create')}}">
        {{isset($Category) ?method_field('put') :''}}
        {{csrf_field()}}

        <div class="input-group   required  col-md-12     margin-bottom-20" id="form-category_name-error">
            <label for="category_name">نام کتگوری:*</label>
            <!-- /.input-group-btn -->
            <input type="text" class="form-control required" id="category_name" name="category_name"
                   value="{{old('category-name',isset($Category) ?$Category->category_name :'')}}"
                   placeholder="نام کتگوری:*">
            <span style="font-size: 25px;" id="category_name-error" class="help-block"></span>
        </div>


        <a href="javascript:ajaxLoad('{{route('category.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward"
                                                                                                 style="margin-right: 5px;"></i></a>
        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save"
                                                                            style="margin-right: 5px;"></i></button>
    </form>
</div>
