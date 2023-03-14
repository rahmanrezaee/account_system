<div class="col-lg-6 col-xs-12">
    <form method="post" id="frm"
          action="{{isset($product) ?route('product.update',$product->product_id):route('product.create')}}">
        {{isset($product) ?method_field('put') :''}}
        {{csrf_field()}}


        <div class="input-group  required  margin-bottom-20 col-md-12" id="form-product_name-error">

            <label for="product_name" id="hid">نام محصول:*</label>
            <!-- /.input-group-btn -->
            <input id="product_name" type="text" class="form-control required" placeholder="نام محصول::*"
                   name="product_name" autocomplete="off"
                   value="{{old('product_name',isset($product)?$product->product_name:'')}}">
            <span id="product_name-error" class="help-block"></span>
        </div>


        <div class="input-group required margin-bottom-20 col-md-12" id="form-product_cod-error">

            <label for="product_cod">کود محصول:*</label>
            <!-- /.input-group-btn -->
            <input id="product_cod" type="text" class="form-control  required" placeholder="کود  محصول::*"
                   name="product_cod" autocomplete="off"
                   value="{{old('product_cod',isset($product)?$product->	product_code:'')}}">
            <span id="product_cod-error" class="help-block"></span>
        </div>
        <div class="input-group  required margin-bottom-20 col-md-12" id="form-procuct_buy_price-error">

            <label for="procuct_buy_price">قیمت خرید محصول:*</label>
            <!-- /.input-group-btn -->
            <input id="procuct_buy_price" type="number" class="form-control required" placeholder="قیمت خرید محصول:*"
                   name="procuct_buy_price" autocomplete="off"
                   value="{{old('procuct_buy_price',isset($product)?$product->procuct_buy_price:'')}}">
            <span id="procuct_buy_price-error" class="help-block"></span>

        </div>
        <div class="input-group  required margin-bottom-20 col-md-12" id="form-product_sale_price-error">

            <label for="product_sale_price">قیمت فروش محصول:*</label>
            <!-- /.input-group-btn -->
            <input id="product_sale_price" type="number" class="form-control required" placeholder="قیمت فروش محصول::*"
                   name="product_sale_price" autocomplete="off"
                   value="{{old('product_sale_price',isset($product)?$product->product_sale_price:'')}}">
            <span id="product_sale_price-error" class="help-block"></span>

        </div>
        <div class="input-group margin-bottom-20 col-md-12">
            <label for="category">انتخاب کتگوری</label>
            <!-- /.input-group-btn -->
            <select name="category" id="category" class="form-control select2_1">

                @foreach( $catgory as  $category)

                    <option value="{{$category->category_id}}"{{(isset($product) && $product->category_id==$category->category_id) ?'selected'.'='.'selected':''}}>{{$category->category_name}}</option>

                @endforeach

            </select>
        </div>
        <div class="input-group margin-bottom-20 col-md-12">
            <label for="unit">انتخاب واحد</label>
            <!-- /.input-group-btn -->
            <select name="unit" id="unit" class="form-control select2_1">

                @foreach($unit as $uni)

                    <option value="{{$uni->unit_id}}"{{(isset($product) && $product->unit_id==$uni->unit_id) ?'selected'.'='.'selected':''}}>{{$uni->unit_name}}</option>
                @endforeach


            </select>
        </div>
        <div class="form-group required" id="form-product_description-error">
            <label for="product_description">توضیحات:*</label>
            <textarea type="text" class="form-control required" id="	product_description" name="product_description"
                      value="" cols="4"
                      placeholder="توضیحات">{{isset($product)? $product->product_description:''}}</textarea>
            <span id="product_description-error" class="help-block"></span>

        </div>


        <a href="" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save"
                                                                            style="margin-right: 5px;"></i></button>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        selectTwo();
        jalali();

    });

</script>
