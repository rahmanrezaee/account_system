<h3 style="margin-right: 2%; margin-bottom: 2%">
        {{isset($panel_title) ?$panel_title :'ثبت شریک'}}
</h3>

<div class="row">
    <div class="col-md-6">
            <form method="post" id="frm" action="{{isset($owner) ?url('owner/update/'.$owner->owner_id):route('owner.create')}}" >
                {{isset($owner) ?method_field('put') :''}}
                {{csrf_field()}}

                        {{-- owner full_name --}}
                            <div class="form-group required" id="form-full_name-error">
                                <label for="full_name" class="col-md-4 control-label">نام و تخلص</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required 
                                    value="{{old('full_name',isset($owner)?$owner-> full_name:'')}}" placeholder="نام و تخلص" autofocus autocomplete="off">
                                <span id="full_name-error" class="help-block"></span>
                            </div>
                
                        {{-- sahm sharakat --}}
                            <div class="form-group required" id="form-percentage-error">
                                <label for="percentage" class="col-md-4 control-label">سهم شراکت</label>
                                <input type="text" class="form-control" id="percentage" name="percentage"
                                    value="{{old('percentage',isset($owner)?$owner-> percentage:'')}}" required placeholder="سهم شراکت" autocomplete="off">
                                <span id="percentage-error" class="help-block"></span>
                            </div>
                            {{-- date share --}}
                            <div class="form-group required" id="form-date_share-error" style="padding-right: 0px;">
                                    <label for="date_share" class="col-md-6 control-label">تاریخ شراکت</label>
                                        <input type="text" placeholder="روز/ماه/سال" class="form-control date-picker required" name="date_share"
                                                   value="{{old('date_share',isset($owner)?$owner-> date_share:"")}}"
                                                   autofocus required>
                                        <span id="date_share-error" class="help-block"></span>
                            </div>
                 
                            <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk" value="ذخیره"> ذخیره</button>
                            <a href="javascript:ajaxLoad('{{ route('owner.list') }}')" class="btn btn-danger glyphicon glyphicon-backward">لغو</a>
                    
              </form>
    </div>
</div>


<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });
        $('.select2_1').select2();

    });
    


</script>

