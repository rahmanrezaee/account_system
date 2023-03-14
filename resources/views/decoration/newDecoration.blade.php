<div class="container">
    <h3 >
        {{isset($panel_title) ?$panel_title :'ثبت انواع دیکوریشن '}}
    </h3>
    <div class="row col-lg-10 col-md-10">

        {{-- decoration registration form --}}
        <form method="post" id="frm" enctype="multipart/form-data" action="">
           
            <div class="col-md-8">
    <button class="btn btn-success" style="float:left; margin-bottom:12px;" data-toggle="modal" data-target="#myModal">ثبت دیکوریشن جدید</button>
            
                {{-- select decoration --}}
                <div class="form-group required" id="form-decoration_name-error">
                    <label for="decoration_name" class="col-md-4 control-label">انتخاب نام دیکوریشن</label>
                   <select name="decoration_name" id="decoration_name" class="form-control required">
                       <option  value="" >دیکوریشن 1</option>
                       <option value ="">دیکوریشن 2</option>
                       <option  value="" >دیکوریشن 3</option>
                       <option value ="">دیکوریشن 4</option>
                   </select>
                    <span id="decoration_name-error" class="help-block"></span>
                </div>

                   {{-- select type of decoration --}}
                   <div class="form-group required" id="form-decoration_type-error">
                    <label for="decoration_type" class="col-md-4 control-label">انواع دیکوریشن</label>
                   <select name="decoration_type" id="decoration_type" class="form-control required">
                       <option  value="" >دیکوریشن 1</option>
                       <option value ="">دیکوریشن 2</option>
                       <option  value="" >دیکوریشن 3</option>
                       <option value ="">دیکوریشن 4</option>
                   </select>
                    <span id="decoration_type-error" class="help-block"></span>
                </div>

                   
            <div class="form-group">
                <div class="col-md-8 col-md-offset-4 register">
                    <a href="" class="btn btn-danger">لغو</a>
                    <button type="submit" id="btn_save" class="btn btn-primary">ثبت</button>
                 
                </div>
            </div>
        </form>
    </div>
</div>


 <!-- Modal -->
 <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
      
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
             
              <h4 class="modal-title" style="text-align:center;">ثبت دیکوریشن جدید</h4>
            </div>
            <div class="modal-body" >
         
                <form method="post" action="">
                    <div class="form-group required" id="decoration_name">
                        <label for="decoration_name" class="col-md-4 control-label">نام دیکوریشن</label>
                        <input type="text" class="form-control" id="decoration_name" name="decoration_name"  placeholder="نام دیکوریشن" autofocus required>
                        <span id="decoration_name-error" class="help-block"></span>     
                    </div>
                           
                             <button type="button" class="btn btn-danger" data-dismiss="modal">لغو</button>
                    <button type="submit" class="btn btn-success">ثبت</button>
                </form>
    
            </div>
          </div>
      
        </div>
      </div>


<script type="text/javascript">


    $(document).ready(function () {
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
