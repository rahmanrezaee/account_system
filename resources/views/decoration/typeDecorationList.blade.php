<div class="row">
    <div class="col-md-12">
        <h3 style="text-align: center">{{isset($panel_title) ?$panel_title :'لیست انواع دیکوریشن'}}</h3>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12">
        {{-- start table section --}}
        <table id="example" class="table table-striped table-bordered display responsive" style="width:100%">
            <thead>
            <tr>
                <th>شماره</th>
                <th>نوع دیکوریشن</th>
                <th>عملیات</th>

            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>شماره</th>
                <th>نوع دیکوریشن</th>
                <th>عملیات</th>

            </tr>
            </tfoot>
            <tbody>

            @foreach($decor_type as $d)

                <tr>

                    <td>{{$d->decor_type_id}}</td>
                    <td>{{$d->type_decor_name}}</td>


                    <td colspan="2">
                        <a href="javascript:if(confirm('Are you want to delete this record?'))ajaxDelete('{{route('decoration.typeDelete',$d->type_decor_name)}}','{{csrf_token()}}')"><i class=" glyphicon glyphicon-trash btn btn-danger btn-sm" ></i></a>

                        <a href="javascript:ajaxLoad('{{route('decoration.details',$d->type_decor_name)}}')"><i class="glyphicon glyphicon-eye-open btn btn-success btn-sm"></i></a>
                    </td>

                </tr>

            @endforeach

            </tbody>
        </table>

    </div>
</div>





<script type="text/javascript">

    $(document).ready(function () {

        $('#example').dataTable();
    })


</script>

</div>

