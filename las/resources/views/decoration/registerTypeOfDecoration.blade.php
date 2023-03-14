<div class="container">
    <h3>
        {{isset($panel_title) ?$panel_title :'ثبت انواع دیکوریشن '}}
    </h3>
    
    <div class="row" style="margin-top:30px;">
    
        <div class="col-sm-12 col-md-12">
            {{-- decoration registration form --}}
            
            <form method="post" id="frm" action="{{route('decoration.register')}}" >
            
            {{csrf_field()}}

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        {{-- select type of decoration --}}
                        <div class="form-group required" id="form-decoration_type-error">
                            <label for="decoration_type" class="control-label">انتخاب نوع دیکوریشن</label>
                            <select name="decoration_type" id="decoration_type" class="form-control required">
                                <option value=""></option>
                                 <option value="دیکوریشن فوق العاده">دیکوریشن فوق العاده</option>
                                 <option value="دیکوریشن اختصاصی">دیکوریشن اختصاصی</option>
                                 <option value="دیکوریشن درجه یک">دیکوریشن درجه یک</option>
                                 <option value="دیکوریشن درجه دو">دیکوریشن درجه دو</option>
                                 <option value="دیکوریشن درجه سه">دیکوریشن درجه سه</option>
                                 <option value="دیکوریشن درجه چهار">دیکوریشن درجه چهار</option>
                                 <option value="دیکوریشن درجه پنج">دیکوریشن درجه پنج</option>
                            </select>
                            <span id="decoration_type-error" class="help-block"></span>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        {{-- select decoration --}}
                        <div class="form-group required" id="form-choose_decoration_name-error">
                            <label for="choose_decoration_name" class="control-label">انتخاب نام دیکوریشن</label>
                            <select name="choose_decoration_name[]" id="choose_decoration_name" class="form-control required" multiple>

                                @foreach($decor as $d)
                                    <option value="{{$d->decor_id}}">{{$d->name}}</option>
                                @endforeach
                            </select>
                            <span id="choose_decoration_name-error" class="help-block"></span>
                        </div>
                        {{--end select decoration --}}
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4 register" >
                            <a href="" class="btn btn-danger glyphicon glyphicon-backward" data-dismiss="modal">لغو</a>
                            <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>   
</div>
<script type="text/javascript">


    $(document).ready(function () {
        $("#choose_decoration_name").select2();
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
