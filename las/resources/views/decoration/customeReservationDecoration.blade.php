<div class="row">
    <div class="col-md-12">
        <h3 style="text-align: center">{{isset($panel_title) ?$panel_title :'لیست سالون ها'}}</h3>
    </div>
</div>

<div class="row">
        <div class="col-sm-12 col-md-12">
         {{-- start table section --}}
                <table id="example" class="table table-striped table-bordered display responsive" style="width:100%">
                    <thead>
                    <tr>
                        <th>شماره</th>
                        <th>مشتری</th>
                        <th>تاریخ</th>
                        <th>ایام هفته</th>
                        <th>شب / روز</th>
                        <th>ساعت</th>
                        <th>بایانه</th>
                        <th>مجموع </th>
                        <th>توضیحات</th>
                        <th>عملیات</th>
                    
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>شماره</th>
                        <th>مشتری</th>
                        <th>تاریخ</th>
                        <th>ایام هفته</th>
                        <th>شب / روز</th>
                        <th>ساعت</th>
                        <th>بایانه</th>
                        <th>مجموع </th>
                        <th>توضیحات</th>
                        <th>عملیات</th>
                        

                    </tr>
                    </tfoot>
                    <tbody>
                        <?php $count  = 1;?>
                        @foreach($cusReserve as $cr)
                        
                        <tr>

                            <td>
                                {{ $count++ }}
                            </td>
                            <td>{{$cr->name}}</td>
                            <td>{{$cr->date_reserve}}</td>
                            <td>{{$cr->week_day}}</td>
                            <td>{{$cr->day_night}}</td>
                            <td>{{$cr->time}}</td>
                            <td>{{$cr->current_payment}}</td>
                            <td>{{$cr->total_payment}}</td>
                            <td>{{$cr->description}}</td>
                            

                            <td colspan="2">
                               <a title="پرنت" target="_blank" href="{{route('decoration.printFactor',$cr->res_decor_id)}}"><i class="fa fa-print btn btn-bordered btn-rounded btn-default btn-xs"></i></a>
                               <a title="بیشتر" href="javascript:ajaxLoad('{{route('decoration.showReserveHallToDecore',$cr->res_decor_id)}}')"><i class="fa fa-navicon btn btn-bordered btn-rounded btn-default btn-xs"></i></a>
                                <a title="حذف" href="javascript:if(confirm('Are you want to delete this record?'))ajaxDelete('{{route('decoration.crDelete',$cr->res_decor_id)}}','{{csrf_token()}}')"><i class=" glyphicon glyphicon-trash btn btn-bordered btn-rounded btn-danger btn-xs" ></i></a>
                                <a title="ویرایش" href="javascript:ajaxLoad('{{route('reserveDecoration.edit',$cr->res_decor_id)}}')"><i class="glyphicon glyphicon-edit btn btn-bordered btn-rounded btn-primary btn-xs"></i></a>
                           </td>

                        </tr>

                    @endforeach
                         
                    </tbody>
                </table>

        </div>
</div>
   


    
      
    <script type="text/javascript">
        
      $(document).ready(function () {
            
        $('#example').dataTable({
            ordering:false
        });
    })
    
           
     </script>

</div>

