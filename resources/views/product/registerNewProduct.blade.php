<div class="container">
    <h3 style="margin-right: 2%">
        {{isset($panel_title) ?$panel_title :'بخش محصولات'}}
    </h3>
    <form method="post" id="frm"
          action="{{isset($product) ?route('product.update',$product->product_id):route('product.create')}}">
        {{isset($product) ?method_field('put') :''}}
        {{csrf_field()}}

        {{--  first row  --}}
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group" id="form-product_name-error">
                    <label for="product_name" class="control-label">نام محصول :</label>
                    <input id="product_name" type="text" class="form-control required" name="product_name"
                           value="{{old('product_name',isset($product)?$product->product_name:'')}}">
                    <span id="product_name-error" class="help-block"></span>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group " id="form-product_code-error">
                    <label for="product_code" class="control-label">کد محصول :</label>
                    <input id="product_code" type="text" class="form-control required" name="product_code"
                           value="{{old('product_name',isset($product)?$product->product_code:'')}}">
                    <span id="product_code-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group " id="form-text_mount-error">
                    <label for="text_mount" class="control-label">فیصدی مالیات :</label>
                    <input id="text_mount" type="text" class="form-control required" name="text_mount"
                           value="{{old('text_mount',isset($product)?$product->text_mount:'0')}}">
                    <span id="text_mount-error" class="help-block"></span>
                </div>
            </div>
        </div>
        {{--  start second row  --}}
        <div class="row">

            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group required" id="form-product_category-error">
                    <label for="product_category" class="control-label">انتخاب کتگوری :</label>

                    <div class="input-group">
                        <select id="product_category" type="text" class="form-control required" name="product_category">

                            @foreach( $category as  $cat)
                                <option value="{{$cat->category_id}}"{{(isset($product) && $product->category_id==$cat->category_id) ?'selected'.'='.'selected':''}}>{{$cat->category_name}}</option>
                            @endforeach

                        </select>
                        <span class="input-group-addon bg-primary text-white">
                             <a data-toggle="collapse" class="text-white" href="#addcategory">اضافیه کردن</a>
                        </span>

                    </div>

                    <span id="product_unit-error" class="help-block"></span>
                    <div id="addcategory" class="panel-collapse collapse padding-30" style="background-color: rgba(231, 231, 231, 0.62);">

                        <div class="form-group">
                            <label for="category_name">نام کتگوری:*</label>
                            <!-- /.input-group-btn -->
                            <input type="text" class="form-control required" id="category_name" name="category_name"
                                   value="{{old('category-name',isset($Category) ?$Category->category_name :'')}}"
                                   placeholder="نام کتگوری:*">
                        </div>


                        <button class="btn btn-danger" data-toggle="collapse" class="text-white"
                                href="#addcategory">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i>
                        </button>
                        <button class="btn btn-primary" type="button" id="btn_save_category">ذخیره<i
                                    class="fa fa-save"
                                    style="margin-right: 5px;"></i></button>

                    </div>

                </div>

                <span id="product_category-error" class="help-block"></span>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group required " id="form-product_unit-error">
                    <label for="product_unit" class="control-label">انتخاب واحد :</label>
                    <div class="input-group">
                        <select name='product_unit' id='product_unit' class="form-control required">
                            <option value="انتخاب واحد"></option>

                            @foreach($unit as $uni)

                                <option value="{{$uni->unit_id}}"{{(isset($product) && $product->unit_id==$uni->unit_id) ?'selected'.'='.'selected':''}}>
                                    {{$uni->unit_quantity}} - {{$uni->unit_name}}
                                </option>
                            @endforeach

                        </select>
                        <span class="input-group-addon bg-primary text-white">
                             <a data-toggle="collapse" class="text-white" href="#addunit">اضافیه کردن</a>
                        </span>

                    </div>

                    <span id="product_unit-error" class="help-block"></span>
                    <div id="addunit" class="panel-collapse collapse padding-30" style="background-color: rgba(231, 231, 231, 0.62);">

                        <div class="input-group  required  margin-bottom-20 col-md-12" id="form-unit_name-error">
                            <label for="unit_name">واحد:*</label>
                            <!-- /.input-group-btn -->
                            <input type="text" class="form-control required" id="unit_name" name="unit_name"
                                   placeholder="واحد">
                            <span style="font-size: 25px;" id="unit_name-error" class="help-block"></span>
                        </div>

                        <div class="input-group  required  margin-bottom-20 col-md-12" id="form-unit_number-error">
                            <label for="unit_number">تعداد:*</label>

                            <!-- /.input-group-btn -->
                            <input type="text" class="form-control required" id="unit_number" name="unit_number"
                                   placeholder="تعداد">
                            <span style="font-size: 25px;" id="unit_number-error" class="help-block"></span>
                        </div>

                        <button data-toggle="collapse" class="text-white btn btn-danger" href="#addunit">لغو<i
                                    class="fa fa-backward" style="margin-right: 5px;"></i></button>
                        <button class="btn btn-primary" type="button" id="btn_save_unit">ذخیره<i class="fa fa-save"
                                                                                                 style="margin-right: 5px;"></i>
                        </button>


                    </div>
                </div>

            </div>
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group " id="form-min_value-error">
                    <label for="min_value" class="control-label">حداقل تعداد در گدام :</label>
                    <input id="min_value" type="text" class="form-control required" name="min_value"
                           value="{{old('min_value',isset($product) ?  $product->min_value:'1000')}}">
                    <span id="min_value-error" class="help-block"></span>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="form-group " id="form-default_product-error">
                    <label for="default_product" class="control-label">قمیت فی پیش فرض :</label>
                    <input id="text_mount" type="text" class="form-control required" name="default_product"
                           value="{{old('default_product',isset($product)?$product->default_sale_product:'0')}}">
                    <span id="default_product-error" class="help-block"></span>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 col-lg-4  col-lg-offset-4 col-md-offset-4">


                <div class="form-group required " id="form-product_description-error">
                    <label for="product_description">توضیحات:*</label>
                    <input type="text" class="form-control required " id="product_description"
                           name="product_description"
                           value="{{isset($product)? $product->product_description:''}}"
                           placeholder="توضیحات">
                    <span id="product_description-error" class="help-block"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4  ">


                <div class="form-group required " id="form-product_description-error">
                    <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره
                    </button>
                    <a href="javascript:ajaxLoad('{{ route('product.list') }}')"
                       class="btn btn-danger glyphicon glyphicon-backward">لغو</a>
                </div>



            </div>
        </div>

        <div class="row">
            <div class="form-group" style="margin-top: 2%">

            </div>
        </div>

    </form>
</div>

<script type="text/javascript">


    $(document).ready(function () {


        $("#btn_save_category").click(function (event) {



            console.log($("#category_name").val());

            let data ={
                'category_name':$("#category_name").val(),
                '_token':'{{ csrf_token() }}'
            };
            addTOdatabase("{{ route('category.create.Manal') }}", data ,'POST').then((response) => {


                if (response){
                    let html = '<option value="'+response.category_id+'">'+response.category_name+'</option>';

                    let category = $("#product_category").html() + html;

                    $("#product_category").html(category);
                }

                $("#addcategory").collapse('hide')



            });

        })
        $("#btn_save_unit").click(function (event) {


            let data ={
                'unit_number':$("#unit_number").val(),
                'unit_name':$("#unit_name").val(),
                '_token':'{{ csrf_token() }}'
            };
            addTOdatabase("{{ route('unit.create.Menual') }}", data ,'POST').then((response) => {


                if (response){
                    let html = '<option value="'+response.unit_id+'">'+   response.unit_quantity + ' - '+response.unit_name+ '</option>';

                    let units = $("#product_unit").html() + html;

                    $("#product_unit").html(units);
                }

                $("#addunit").collapse('hide')



            });

        })




        // show unit register modal
        $("#product_unit").on("change", function () {
            $modal = $('#myModal');
            if ($(this).val() === 'new_unit') {
                $modal.modal('show').change();

            }
        });

        // show category register modal
        $("#product_category").on("change", function () {
            $modal = $('#categoryModal');
            if ($(this).val() === 'new_category') {
                $modal.modal('show').change();
            }
        });
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

        $('#new_unit').bind('change', function () {
            if (this.value === 'create_new_unit') {
                $('#mayModal').modal('show');
            }
        });

    });
</script>
