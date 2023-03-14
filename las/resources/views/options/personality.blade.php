<script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>



<h3 class="box-title text-center">
    {{isset($panel_title) ?$panel_title :''}}
</h3>
<div class="col-md-6 col-lg-6 col-md-offset-3">
    <form method="post" id="frm" action="{{ route('options.personality') }}">

        {{csrf_field()}}
        <div class="form-group ">
            <label for="companyName">نام شرکت:</label>
            <input name="companyName" id="companyName" value="{{ get_options("companyName")->value('option_value') }}"
                   class="form-control select2_1 companyName">
        </div>
        <div class="form-group ">
            <label for="companyNameEnglish">نام شرکت در انگلیسی:</label>
            <input name="companyNameEnglish" id="companyNameEnglish" value="{{ get_options("companyNameEnglish")->value('option_value') }}"
                   class="form-control select2_1 companyNameEnglish">
        </div>
        <div class="form-group ">
            <label for="ownerName">نام :</label>
            <input name="ownerName" id="ownerName" value="{{ get_options("ownerName")->value('option_value') }}"
                   class="form-control select2_1 ownerName">
        </div>
        <div class="form-group ">
            <label for="customer_type">تخلض و با فامیل :</label>
            <input name="ownerFamily" id="ownerFamily" value="{{ get_options("ownerFamily")->value('option_value') }}"
                   class="form-control select2_1 ownerFamily">
        </div>

        <div class="form-group ">
            <label for="customer_type">شماره تلفون :</label>
            <input name="ownerPhone" id="ownerPhone" type="text" value="{{ get_options("ownerPhone")->value('option_value') }}"
                   class="form-control select2_1 ownerPhone">

        </div>

        <div class="form-group ">
            <label for="customer_type">ایمیل :</label>
            <input name="ownerEmail" id="ownerEmail" type="email" value="{{ get_options("ownerEmail")->value('option_value') }}"
                   class="form-control select2_1 ownerEmail">

        </div>

        <div class="form-group ">
            <label for="customer_type">آدرس :</label>
            <input name="ownerAddress" type="text" id="ownerAddress" value="{{ get_options("ownerAddress")->value('option_value') }}"
                   class="form-control select2_1 ownerAddress">

        </div>

        <div class="form-group ">
            <label for="customer_type">پایین صفحه :</label>

        <textarea name="FooterContentFactor" id="editor">  {!! get_options("FooterContentFactor")->value('option_value') !!}</textarea>
           </div>
        <div class="form-group col-md-6 col-lg-6 ">
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_save">
                ذخیره
                اطلاعات <i class="fa fa-save"></i>
            </button>
        </div>
    </form>


</div>
<div class="col-md-۳3 col-lg-3 ">

    <form enctype="multipart/form-data" method="post" id="logoform" action="{{ route('options.Logo') }}">

        {{ csrf_field() }}
        <div class="form-group">


            <input name="logo"
                   style='height: 0px;width:0px; overflow:hidden;'
                   id="photo" type='file'
                   onchange="imageSave(this);"/>

            <img id="logoImage" style="height: 100%;width: 100% "
                 src="{{ get_options('CompanyLogo') == null ? asset('image/unnamed.png') : asset( get_options('CompanyLogo')->value('option_value') ) }}"
                 alt="your image"/>

            <label for="photo"  class="form-control btn btn-default">انتخاب
                عکس :</label>

            <p class="help-block">عکس تان کمتر از 2 Mb باشد.</p>
            <p class="help-block">عکس تان چهار شنگ باشد برای بهتر دیده شدن.</p>
        </div>

    </form>




</div>

<script type="text/javascript">



    function imageSave(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {


                var form = $("#logoform");
                var data = new FormData($(form)[0]);
                var url = form.attr("action");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {

                        $('#logoImage').attr('src',data.url)
                    },
                    error: function (xhr, textStatus, errorThrown) {

                    }
                });


            };

            reader.readAsDataURL(input.files[0]);
        }
    }


    $(document).ready(function () {


        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .catch( error => {
                console.error( error );
            } );


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

            buttonsColor: "پیش فرض ",

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

