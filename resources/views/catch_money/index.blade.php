<div class="col-md-12">
    <h4 style="text-align: center">
        {{isset($panel_title) ? $panel_title :''}}
    </h4>

    <div class="row margin-top-20">
        <div class="col d-flex col-md-12">
            <div class="col col-md-3">
                <div class="form-group">
                    <label for="employee_id">کارمند مورد نظر</label>
                    <select name="employee_id" id="employee_id" class="form-control">
                        @if (isset($employees))
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->employee_id }}">{{ $employee->first_name }} &nbsp;&nbsp;{{ $employee->last_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col col-md-3">
                <div class="form-group" id="form-payment_status-error">
                    <label for="payment_status">انتخاب برداشت یا پرداخت</label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="draw">برداشت</option>
                        <option value="pay">پرداخت</option>
                    </select>
                </div>
            </div>
            <div class="col col-md-2 d-flex align-items-center">
                <button class="btn btn-info btn-rounded" id="report-btn"> نمایش اطلاعات &nbsp;<i class="report_icon"></i></button>

            </div>
            <div class="col col-md-1 d-flex align-items-center">


            </div>
            <div class="col col-md-2 d-flex align-items-center">
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
            <div class="col col-md-1 d-flex align-items-center">
                    <button class="btn btn-primary form-control" onclick="imprimir()"> پرنت <i class="fa fa-print"></i></button>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-2">

            </div>

        </div>
    </div>
    <div class="row margin-top-20">
        <div class="col-sm-3 col-lg-3">
            <p>
                مجموع پرداختی ها
            </p>
        </div>
        <div class="col-sm-3 col-lg-3">
            <p id="count_of_payment">
                0
            </p>
        </div>
        <div class="col-sm-3 col-lg-3">
            <p>
                مجموع برداشتی ها
            </p>
        </div>
        <div class="col-sm-3 col-lg-3">
            <p id="count_of_catch">
                0
            </p>
        </div>
    </div>
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th >شماره</th>
            <th>نوعیت حساب</th>
            <th>اسم کارمند</th>
            <th>برداشت | پرداخت</th>
            <th> مقدار پول </th>
            <th>تاریخ</th>
            <th>توضیحات</th>
            <th id="operations_title">عملیات</th>
        </tr>
        </thead>
        <tbody id="content-display">

        </tbody>
    </table>
    <div class="pagination" style="float: left" id="pagination">

    </div>
</div>
<script>

    $(document).ready(function () {
        // search section
        $(document).off('click').on('click','#report-btn',function () {
            var search = $('#employee_id').val();
            var status_payment = $('#payment_status').val();


            if (search != '' && status_payment !='') {
                $(".report_icon").addClass('fa fa-spinner fa-spin');
                $('.loading').show();

                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: 'catch_money/search/' + search + '/' + status_payment,
                    success: function (response) {
                        var table = "";
                         if (response){

                             var data = response['data'].data;


                             if (data.length > 0)
                             {
                                 for(var i = 0; i<data.length ; i++){
                                     if (data[i].status_payment == 'pay'){
                                         table += '<tr>' +
                                             '<td>' + data[i].id + '</td>' +
                                             '<td>' + data[i].name + '</td>' +
                                             '<td>' + data[i].first_name + '&nbsp;' + data[i].last_name + '</td>' +
                                             '<td>پرداخت</td>' +
                                             '<td>' + data[i].amount + '</td>' +
                                             '<td>' + data[i].date + '</td>' +
                                             '<td>' + data[i].description + '</td>' +
                                             '<td>' +
                                             '<button  id-item =' + data[i].id + ' class="glyphicon glyphicon-edit btn btn-primary btn-sm edit_catch_money" ></button>'
                                             + '</td>' +
                                             '</tr>';
                                     } else {

                                         table += '<tr>' +
                                             '<td>' + data[i].id + '</td>' +
                                             '<td>' + data[i].name + '</td>' +
                                             '<td>' + data[i].first_name + '&nbsp;' + data[i].last_name + '</td>' +
                                             '<td>برداشت</td>' +
                                             '<td>' + data[i].amount + '</td>' +
                                             '<td>' + data[i].date + '</td>' +
                                             '<td>' + data[i].description + '</td>' +
                                             '<td>' +
                                             '<button  id-item =' + data[i].id + ' class="glyphicon glyphicon-edit btn btn-primary btn-sm edit_catch_money" ></button>'
                                             + '</td>' +
                                             '</tr>';

                                     }
                                 }
                             } else {
                                 table += '<tr><td colspan="8" class="text-center"> اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                             }

                             // $('#pagination').html("first page");
                             $('#count_of_payment').html(response.count_of_payment);
                             $('#count_of_catch').html(response.count_of_catch);

                             $('#pagination').html(response['pagination']);


                         }

                        $('#content-display').html(table);

                        $(".report_icon").removeClass('fa fa-spinner fa-spin');
                        $('.loading').hide();

                    }
                })
            }

            //edit detail section
            $("#content-display").on('click', ".edit_catch_money", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                javascript:ajaxLoad("catch_money/update/" + id);
            });

            // Delete payment_money
            $("#content-display").on('click', ".delete_catch_money", function () {

                var id = $(this).attr("id-item");
                //alert(id)
                if (confirm('Are you want to delete this record?')){
                    javascript:ajaxDelete("catch_money/delete/"+ id , $('meta[name="csrf-token"]').attr('content'));
                }
            });

        })

    })


    // Pagination
    $(document).on('click','.pagination a', function (event) {
        event.preventDefault();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var myurl = $(this).attr('href');
        $('.loading').show();
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: myurl,
            success: function (response) {

                var data = response['data'].data;
                var table = "";

                for (var i = 0; i < data.length; i++) {

                    if (data[i].status_payment == 'pay'){
                        table += '<tr>' +
                            '<td>' + data[i].id + '</td>' +
                            '<td>' + data[i].name + '</td>' +
                            '<td>' + data[i].first_name + '&nbsp;' + data[i].last_name + '</td>' +
                            '<td>پرداخت</td>' +
                            '<td>' + data[i].amount + '</td>' +
                            '<td>' + data[i].date + '</td>' +
                            '<td>' + data[i].description + '</td>' +
                            '<td>' +
                            '<button  id-item =' + data[i].id + ' class="glyphicon glyphicon-edit btn btn-primary btn-sm edit_catch_money" ></button>'
                            + '</td>' +
                            '</tr>';
                    } else {

                        table += '<tr>' +
                            '<td>' + data[i].id + '</td>' +
                            '<td>' + data[i].name + '</td>' +
                            '<td>' + data[i].first_name + '&nbsp;' + data[i].last_name + '</td>' +
                            '<td>برداشت</td>' +
                            '<td>' + data[i].amount + '</td>' +
                            '<td>' + data[i].date + '</td>' +
                            '<td>' + data[i].description + '</td>' +
                            '<td>' +
                            '<button  id-item =' + data[i].id + ' class="glyphicon glyphicon-edit btn btn-primary btn-sm edit_catch_money" ></button>'
                            + '</td>' +
                            '</tr>';

                    }

                }
                $('.loading').hide();
                $('#content-display').html(table);
                // $('#pagination').html("first page");
                $('#count_of_payment').html(response.count_of_payment);
                $('#count_of_catch').html(response.count_of_catch);

                $('#pagination').html(response['pagination']);

            }
        })
    })

    // Pagination According to Selecting Number
    $(".list-page").change(function () {
        var search = $('#employee_id').val();
        var status_payment = $('#payment_status').val();

        if (search != '' && status_payment !='') {
            $('.loading').show();

            $.ajax({
                type: 'get',
                dataType: 'json',
                url: 'catch_money/search/' + search + '/' + status_payment+"/"+$(this).val(),
                success: function (response) {
                    var data = response['data'].data;
                    var table = "";

                    if (data.length > 0)
                    {
                        for(var i = 0; i<data.length ; i++){
                            table += '<tr>'+
                                '<td>' +data[i].id+'</td>'+
                                '<td>' +data[i].name+'</td>'+
                                '<td>' +data[i].first_name+ '&nbsp;'+ data[i].last_name+'</td>'+
                                '<td>' +data[i].status_payment+'</td>'+
                                '<td>' +data[i].amount+'</td>'+
                                '<td>' +data[i].date+'</td>'+
                                '<td>' +data[i].description+'</td>'+
                                '<td>' +
                                '<button  id-item =' + data[i].id + ' class="glyphicon glyphicon-edit btn btn-primary btn-sm edit_catch_money" ></button>'
                                +'</td>'+
                                '</tr>';
                        }
                    } else {
                        table += '<tr><td colspan="8" class="text-center"> اطلاعاتی برای نمایش وجود ندارد</td></tr>'
                    }
                    $('.loading').hide();

                    $('#content-display').html(table);
                    // $('#pagination').html("first page");
                    $('#count_of_payment').html(response.count_of_payment);
                    $('#count_of_catch').html(response.count_of_catch);

                    $('#pagination').html(response['pagination']);


                }
            })
        }

        //edit detail section
        $("#content-display").on('click', ".edit_catch_money", function () {

            var id = $(this).attr("id-item");
            //alert(id)
            javascript:ajaxLoad("catch_money/update/" + id);
        });

        // Delete payment_money
        $("#content-display").on('click', ".delete_catch_money", function () {

            var id = $(this).attr("id-item");
            //alert(id)
            if (confirm('Are you want to delete this record?')){
                javascript:ajaxDelete("catch_money/delete/"+ id , $('meta[name="csrf-token"]').attr('content'));
            }
        });

    })

</script>

<script type="text/javascript">
    function imprimir() {

        var company = $("#company_id option:selected").text();

        var table = document.getElementById("example");
        var d = "<html><head>" +
            "<link rel='stylesheet' href='{{ asset("assets/plugin/bootstrap/css/bootstrap.min.css") }}' >" +
            "<style> th{text-align:right !important} th:last-child,td:last-child{display: none;} body{font-family:sahle}</style>"+
            "</head><body style='direction: rtl;font-family:sahel'>"+ "<h1 class='text-center'>لیست برداشت و پرداخت پول: "+company+" </h1>"+ table.outerHTML + "</body></html>";


        newWin = window.open();
        newWin.document.write(d);
        newWin.setTimeout(function () {

            newWin.print();
            newWin.close();
        },3000)



    }
</script>