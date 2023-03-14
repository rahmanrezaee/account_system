<div class="col-md-12">
    <h4 style="text-align: center">
        {{isset($panel_title) ? $panel_title :''}}
    </h4>
    <div class="row margin-bottom-10">
        <div class="col-md-2">

        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-6"></div>
        <div class="col-md-3">
            <div class="input-group pull-left">
                <a href="javascript:ajaxLoad('{{ route('currency.create') }}')" class="btn btn-info">اضافه کردن ارز
                    <i class="fa fa-pulse"></i>
                </a>
            </div>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>نام ارز</th>
            <th>سمبول ارز</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">
            @foreach($currencies as $currency)
            <tr>
                <td>{{$currency->currency_id}}</td>
                <td>{{$currency->currency_name}}</td>
                <td>{{$currency->symbol}}</td>

                <td id="operations">
                    <a href="javascript:ajaxLoad('{{route('currency.update',$currency->currency_id)}}')">
                        <span><i class="glyphicon glyphicon-edit" title="ویرایش" ></i></span>
                    </a>
                    <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('currency.delete',$currency->currency_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
 </div>

</div>

