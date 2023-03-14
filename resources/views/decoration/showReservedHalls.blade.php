<div class="container">
    {{--  tab headers  --}}

    <div class="tabbable boxed parentTabs">


        <h3 class="text-center">لیست دیکوریش ها سالون
            <a href="javascript:ajaxLoad('{{ route("decoration.list") }}')" class="btn btn-rounded btn-bordered btn-danger btn-xs glyphicon glyphicon-forward"
               data-dismiss="modal">  برگشت  </a>
        </h3>
        <hr>


        <div class="row">
            @foreach($list as $li => $value)
                <div class="col-md-4">

                    <div class="list-group">

                        <span class="list-group-item text-center " style="background-color: #1d84df;color: white"><h4>{{ explode("|",$li)[0] }} ({{ $value[0]->decor_type_name }})
                                <button  onclick="showModel('{{ explode("|",$li)[1] }}')" style="padding: 0px 8px;"  class="btn btn-rounded btn-sm  text-center text-info"><i class="fa fa-plus" style="margin-top: 7px"> </i></button></h4></span>

                        @foreach($value as $l)
                            <span class="list-group-item">{{ $l->dname }}
                                <a href="javascript:if(confirm('Are you want to delete this record?'))ajaxDelete('{{route('decoration.itemDelete',$l->cdid)}}','{{csrf_token()}}')"><i class="fa fa-remove " style="float: left" > حذف </i></a>
                            </span>

                        @endforeach


                    </div>

                </div>
            @endforeach

        </div>


    </div>

</div>

<script>


    function showModel(id){


        $("#hall_id").val(id);
        $("#myModal").show()

    }
    $(document).ready(function () {




        $("#table").dataTable();
        $("#decoration_type").change(function () {

            var cust = $("#chose_customer").val();
            var decor_type = $("#decoration_type").val();


            $.ajax({
                type: "get",
                url: "decoration/getDecore/" + decor_type,
                contentType: false,
                success: function (data) {




                    $('#decore_name').html(data.table_data);

                    console.log(data)


                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });

        });
    })
</script>
<div id="myModal"   class="modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" style="margin-top:20%; display: block;">
            <div class="modal-header">

                <h4 class="modal-title" style="text-align:center;">ثبت سالون جدید </h4>
            </div>
            <div class="modal-body" >

                <form method="post" id="frm" action="{{  route('decoration.addInCuDecor') }}" >

                    {{csrf_field()}}


                    <input type="hidden" name="customer_id" id="customer_id" value="{{ $id_res->customer_id }}">
                    <input type="hidden" name="hall_id" id="hall_id" value="">
                    <input type="hidden" name="reserve_decor_id" id="reserve_decor_id" value="{{ $id_res->reserve_decor_id }}">

                    <div class="form-group required" >
                        <label for="decoration_type" class="control-label">انتخاب نوع دیکوریشن :</label>
                        <select name='decoration_type' id='decoration_type' class="form-control select-ajax">
                            <option value='None'>یکی را انتخاب کنن</option>

                            @foreach($decor_type as $d)
                                <option value="{{ $d->type_decor_name }}">{{$d->type_decor_name}}</option>
                            @endforeach
                        </select>
                        <span id="decoration_type-error" class="help-block"></span>
                    </div>
                    <div class="form-group required" id="hall_name">
                        <label for="hall_name" class="col-md-4 control-label">دیکوریشن ها</label>
                        <select  class="form-control" id="decore_name" name="decore_name" required >


                        </select>
                        <span id="hall_name-error" class="help-block"></span>
                    </div>

                    <div class="form-group" style="margin-bottom: 10%;">
                        <div class="col-md-6 col-md-offset-4 register" >
                            <button class="btn btn-danger glyphicon glyphicon-backward" onclick=" $('#myModal').hide() ">لغو</button>
                            <button type="submit" id="btn_save" class="btn btn-primary glyphicon glyphicon-floppy-disk"> ذخیره</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>