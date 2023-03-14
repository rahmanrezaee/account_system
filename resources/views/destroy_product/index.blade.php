
<h4 class="box-title">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>شماره</th>
            <th>نام گدام</th>
            <th>عملیات</th>



        </tr>
        </thead>
        <tbody>
            @foreach($destories as $key=>$d)

                <tr>
                    <td>{{++$key}}</td>
                    <td>{{$d->store_name}}</td>
                    <td><a href="javascript:ajaxLoad('{{route('destroy_product.edit',$d->store_id)}}')" title="Edit Factor"><span><i class="glyphicon glyphicon-edit"></i></span></a>

                        <a href="javascript:ajaxDelete('{{route('destroy_product.delete',$d->store_id)}}','{{csrf_token()}}')"><span><i class="glyphicon glyphicon-trash "></i></span></a>
                        <a href="javascript:ajaxLoad('{{route('destroy_product.detail',$d->store_id)}}')"><span><i class="glyphicon glyphicon-eye-open "></i></span></a>

                    </td>

                </tr>

                @endforeach

        </tbody>


    </table>

</div>
<script>
    $(document).ready(function () {
        datatable();
    })
</script>

