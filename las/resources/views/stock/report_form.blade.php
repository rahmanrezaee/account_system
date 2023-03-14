<div class="row">
    <div class="col-md-12">
        <div class="form-row">

            <div class="col-md-3">
                <select name="stack_name" id="stack_name" class="form-control select2_1 dynamic" data-state="company">
                    <option value="0" disabled="true" selected="true">انتخاب گدام</option>
                    @foreach($stores as $store)
                        <option value="{{$store->store_id}}">{{$store->store_name}}</option>
                    @endforeach
                    {{csrf_token()}}
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group" >
                    <button class="btn btn-info btn-rounded" id="report-btn"> گزارش گرفتن&nbsp;<i class="report_icon"></i></button>
                </div>
            </div>
            <div class="col-md-3">

            </div>
            <div class="col-md-2 " >
                <button class="btn btn-primary btn-rounded form-control" onclick="imprimir()"> چاپ کردن <i class="fa fa-print"></i></button>
            </div>
            <div class="col-md-2">
                <div class="form-group" >
                    <select class="list-page form-control" >
                        <option>select count</option>
                        <option>5</option>
                        <option>10</option>
                        <option>20</option>
                        <option>50</option>
                    </select>
                </div>
            </div>

        </div><!--end row-->


        <div class="form-row">

            <div class="col-md-12 table-responsive">
                <hr style="margin-top: 0px;">
                <table id="example" class="table table-bordered table-striped " style="margin-bottom: 8px;"
                       cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <td>شماره</td>
                        <th>نام محصول</th>
                        <th>کود محصول</th>
                        <th>تعداد در گدام</th>

                        <th>واحد محصول</th>

                    </tr>
                    </thead>
                    <tfoot id="stack_total">

                    </tfoot>

                    <tbody id="stock_report">


                    </tbody>

                </table>
                <div class="pagination" style="float: left" id="pagination">

                </div>
            </div>
        </div>

    </div>
</div> <!--</row>-->

<script type="text/javascript">

    $(document).ready(function () {

        $(document).off('click').on('click', '#report-btn', function () {

            var stack_id = $('#stack_name').val();
            var _token = $('input[name="_token"]').val();

            var stock = $("#stack_name option:selected").text();

            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();
            $.ajax({
                url: "{{route('search.report')}}",
                method: 'post',
                data: {_token: _token, stack_id: stack_id},
                dataType: 'json',
                success: function (response) {
                    var data = response['data'].data;
                    var table = "";

                    if (data.length > 0){
                        for(var i = 0; i<data.length ; i++){


                            if (data[i].quantity > data[i].min_value ){
                                table += '<tr>'+
                                    '<td>' + (i+1)+'</td>'+
                                    '<td>' +data[i].product_name+'</td>'+
                                    '<td>' +data[i].product_code+ '</td>'+


                                    '<td >' +data[i].quantity+ '</td>'+
                                    '<td>' +data[i].unit_name+ '</td>'+
                                    '</tr>';
                            }else {
                                table += '<tr style="background-color: rgba(169,41,40,0.56);color: white;">'+
                                    '<td>' + (i+1)+'</td>'+
                                    '<td>' +data[i].product_name+'</td>'+
                                    '<td>' +data[i].product_code+ '</td>'+


                                    '<td >' +data[i].quantity+ '</td>'+
                                    '<td>' +data[i].unit_name+ '</td>'+
                                    '</tr>';

                            }

                        }
                        $(".report_icon").removeClass('fa fa-spinner fa-spin');
                        $('.loading').hide();


                        var tr = '<tr>';
                        tr += '<th colspan="1"> تعداد محصول ثبت شده</th>';
                        tr += '<th >' + response.name + '</th>';
                        tr += '<th colspan="1">مجموع محصولات ثبت شده</th>';
                        tr += '<th colspan="2">' + response.total + '</th>';
                        tr += '</tr>';


                    } else {
                        table += '<tr><td colspan="5" class="text-center">اطلاعات برای نمایش وجود ندارد</td></tr>'
                    }

                    $('#stock_report').html(table);
                    $('#pagination').html(response['pagination']);

                    $('#stack_total').html(tr);
                    // table.column(4).visible(false);
                    $(".dt-button").addClass("btn");

                },
                error: function (xhr) {
                    alert(xhr.responseText);

                }

            })

        })
    })



    $(document).ready(function () {
        // getStackReport();
        selectTwo();
    })



    // Pagination
    $(document).on('click','.pagination a', function (event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var url = $(this).attr("href");

        var stack_id = $('#stack_name').val();
        var _token = $('input[name="_token"]').val();

        var stock = $("#stack_name option:selected").text();

        $('.loading').show();
        $.ajax({
            url: url,
            method: 'post',
            data: {_token: _token, stack_id: stack_id},
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                for(var i = 0; i<data.length ; i++){
                    table += '<tr>'+
                        '<td>' + (i+1)+'</td>'+
                        '<td>' +data[i].product_name+'</td>'+
                        '<td>' +data[i].product_code+ '</td>'+
                        '<td>' +data[i].quantity+ '</td>'+
                        '<td>' +data[i].unit_name+ '</td>'+
                        '</tr>';
                }
                $('.loading').hide();
                $('#stock_report').html(table);
                $('#pagination').html(response['pagination']);

                var tr = '<tr>';
                tr += '<th colspan="1"> تعداد محصول ثبت شده</th>';
                tr += '<th >' + response.name + '</th>';
                tr += '<th colspan="1">مجموع محصولات ثبت شده</th>';
                tr += '<th colspan="2">' + response.total + '</th>';
                tr += '</tr>';

                $('#stack_total').html(tr);

                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");

            }
        });
    })

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var stack_id = $('#stack_name').val();
        var _token = $('input[name="_token"]').val();

        var stock = $("#stack_name option:selected").text();

        $('.loading').show();
        $.ajax({
            url: "{{route('search.report')}}"+"/"+$(this).val(),
            method: 'post',
            data: {_token: _token, stack_id: stack_id},
            dataType: 'json',
            success: function (response) {
                var data = response['data'].data;
                var table = "";

                if (data.length > 0){
                    for(var i = 0; i<data.length ; i++){
                        table += '<tr>'+
                            '<td>' + (i+1)+'</td>'+
                            '<td>' +data[i].product_name+'</td>'+
                            '<td>' +data[i].product_code+ '</td>'+
                            '<td>' +data[i].quantity+ '</td>'+
                            '<td>' +data[i].unit_name+ '</td>'+
                            '</tr>';
                    }
                    $('.loading').hide();


                    var tr = '<tr>';
                    tr += '<th colspan="1"> تعداد محصول ثبت شده</th>';
                    tr += '<th >' + response.name + '</th>';
                    tr += '<th colspan="1">مجموع محصولات ثبت شده</th>';
                    tr += '<th colspan="2">' + response.total + '</th>';
                    tr += '</tr>';

                } else {
                    table += '<tr><td colspan="5" class="text-center">اطلاعات برای نمایش وجود ندارد</td></tr>'
                }

                $('#stock_report').html(table);
                $('#pagination').html(response['pagination']);

                $('#stack_total').html(tr);
                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");

            },
            error: function (xhr) {
                alert(xhr.responseText);

            }

        })

    })
</script>

<script type="text/javascript">
    function imprimir() {

        var stack_name = $("#stack_name option:selected").text();

        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} body{font-family:sahle}</style>"+
            "</head><body style='direction: rtl;font-family:sahel'>"+ "<h1 class='text-center'> گزارش موجودی گدام : "+stack_name+" </h1>"+ table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        },3000)



    }
</script>

