<h4 >
    {{isset($panel_title) ?$panel_title :''}}
</h4>

    <div class="col-lg-6 col-md-6 col-xs-12">
        <form action="{{ isset($stock)?route('store.update',$stock->store_id):route('store.create') }}" method="post" id="frm">
            {{csrf_field()}}

           <div class="form-group required" id="form-store_name-error">
               <label for="store_name">نام گدام:*</label>
               <input type="text" value="{{ isset($stock) ? $stock->store_name: "" }}" name="store_name" id="store_name" class="form-control required">
               <span id="store_name-error" class="help-block"></span>
           </div>

            <div class="form-group required" id="form-store_name-error">
                <label for="store_name">صندق پول:*</label>
                <select name="store_money" id="store_money" class="form-control required store_money">
                    @foreach($moneyStore as $s)
                        <option {{ isset($stock) ? $stock->money_store_id == $s->store_id ? "selected"  : "" : "" }} value="{{ $s->store_id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <span id="store_money-error" class="help-block"></span>
            </div>
            @can('isManager')
                <div class="form-group required" id="form-store_name-error">
                    <label for="agency_id">انتخاب نماینده گی:*</label>
                    <select name="agency_id" id="agency_id" class="form-control required">
                        @foreach($agencies as $agency)
                            <option {{ isset($stock) ? $stock->agency_id == $agency->agency_id ? "selected"  : "" : "" }} value="{{ $agency->agency_id }}">{{ $agency->agency_name }}</option>
                        @endforeach
                    </select>
                    <span id="store_money-error" class="help-block"></span>
                </div>
            @endcan

            <div class="form-group required" id="form-store_address-error">
                <label for="store_address">آدرس:*</label>
                <textarea type="text"  name="store_address" id="store_address" class="form-control required" cols="4" rows="4">{{ isset($stock) ? $stock->store_address: "" }}</textarea>
                <span id="store_address-error" class="help-block"></span>
            </div>
            <button class="btn btn-primary" type="submit" id="btn_save">ذخیره<i class="fa fa-save" style="margin-right: 5px;"></i></button>

            <a href="javascript:ajaxLoad('{{route('store.list')}}')" class="btn btn-danger">لغو<i class="fa fa-backward" style="margin-right: 5px;"></i></a>
        </form>

    </div>
    <!-- /.col-lg-4 ol-xs-12 -->

<script>
    selectTwo();
</script>

