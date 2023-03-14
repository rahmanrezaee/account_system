
    <div class="row">
        <h3 style="margin-right: 2%; margin-bottom: 2% ;text-align: center">
            {{isset($panel_title) ?$panel_title :''}}
        </h3>

       {{-- table for list the customer --}}
    <div class="col-md-12 col-sm-12">
            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>شماره دیکوریشن</th>
                        <th>نام دیکوریشن</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
        
                <tbody>
                  
                   @foreach($decor as $d)
                        
                        <tr>
                            <td>{{$d->decor_id}}</td>
                            <td>{{$d->name}}</td>
                            <td colspan="2">
                                <a href="javascript:if(confirm('do you want to delete this record')) ajaxDelete('{{route('decoration.delete',$d->decor_id)}}','{{csrf_token()}}') "><span><i class="glyphicon glyphicon-trash"></i></span></a>
                                
                                <a href="javascript:ajaxLoad('{{route('decoration.update',$d->decor_id)}}')" class="open-modal"><span><i class="glyphicon glyphicon-edit" ></i></span></a >
                            </td>
                        </tr>

                    @endforeach
                         
                </tbody>
            </table>
        </div>


    </div>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" style="margin-top:20%; ">
                <div class="modal-header">

                    <h4 class="modal-title" style="text-align:center;">ثبت دیکوریشن جدید </h4>
                </div>
                <div class="modal-body" >

                    <form method="post" id="send_form" action="{{route('decoration.create')}}" >
                        {{csrf_field()}}
                        <div class="form-group required" id="decoration_name">
                            <label for="decoration_name" class="col-md-4 control-label">نام دیکوریشن</label>
                            <input type="text" class="form-control" id="decoration_name" name="decoration_name" placeholder=""  required>
                            <span id="decoration_name-error" class="help-block"></span>
                        </div>

                        <div class="form-group" style="margin-bottom: 10%;">
                            <div class="col-md-6 col-md-offset-4 register" >
                                <a href="" class="btn btn-danger glyphicon glyphicon-backward" data-dismiss="modal">لغو</a>
                                <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk"  > ذخیره</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
