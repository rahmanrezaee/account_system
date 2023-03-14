<div class="container">
    
    <div class="row">
        <h3 style="margin-right: 3%">ثبت نوع مصرف</h3>
        <div class="col-md-8 col-md-offset-2">
            <form method="post" id="frm" action="{{isset($reason_pay) ?url('reason_pay/update/'.$reason_pay->expense_reason_id):route('reason_pay.create')}}" >
                {{isset($reason_pay) ?method_field('put') :''}}
                {{csrf_field()}}
                <div  class="form-group required" id="form-title-error">
                    <label for="title">نوع مصرف:</label>
                    <input type="text" class="form-control required" id="title" name="title" value="{{old('title',isset($reason_pay) ?$reason_pay->title :'')}}">
                    <span id="title-error" class="help-block"></span>
                </div>
           
                <div class="form-group">
                    <a href="javascript:ajaxLoad('reason_pay')" class="btn btn-danger">لغو</a>
                    <button type="submit" id="btn_save" class="btn btn-primary">‌ذخیره</button>
                </div>
            </form>

        </div>
    </div>
</div>
