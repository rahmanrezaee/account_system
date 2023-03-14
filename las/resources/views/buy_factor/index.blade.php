<h4 class="" style="text-align: center">
    {{isset($panel_title) ?$panel_title :''}}
</h4>
<div class="form-group col-md-3" style="text-align: right">
    <select name="company_id" id="company_id" class="form-control select2_1 dynamic" data-state="company">
        <option value="0" disabled="true" selected="true">انتخاب شرکت</option>
        @foreach($company as $comp)
            <option value="{{$comp->company_id}}">{{$comp->company_name}}</option>
        @endforeach
        {{csrf_token()}}
    </select>
</div>

<div class="col-md-2">
    <button class="btn btn-info btn-rounded" id="report-btn"> گزارش&nbsp;<i class="report_icon"></i></button>
</div>

<div class="col-md-5"></div>
<div class="col-md-2">
    <div class="form-group">
        <select class="list-page form-control" >
            <option>select count</option>
            <option>5</option>
            <option>10</option>
            <option>20</option>
            <option>50</option>
        </select>
    </div>
</div>

<div class="col-md-12">
    <table id="example" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>شماره</th>
            <th>شرکت</th>
            <th>گدام</th>
            <th>شماره فاکتور</th>
            <th>تاریخ خرید</th>
            <th>تخفیف</th>
            <th>پرداختی فعلی</th>
            <th>پرداختی کل</th>
            <th>علمیات</th>
        </tr>
        </thead>
        <tbody id="factor_report" class="conten-display">

        </tbody>
        <tfoot id="factor_total">

        </tfoot>


    </table>
    <div class="pagination" style="float: left" id="pagination">

    </div>
</div>

<div id="myModal" class="modal" style="top: 100px; " tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">اجناس شامل فکتور</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">
                <table class="table">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">نام محصول</th>
                        <th scope="col">تعداد</th>
                        <th scope="col">قیمت</th>
                        <th scope="col">مجموعه قیمت</th>
                    </tr>
                    </thead>
                    <tbody class="modal-content-in">

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>


    function showDetail(id) {

        $.ajax({
            url: "buy_factor/detail/" + id,
            method: 'get',
            dataType: 'json',
            async: true,
            cache: false,
            success: function (data) {

                var res = "";
                data.forEach((item, index) => {

                    res += "<tr><td>" + item.product_name + "</td> <td>" + item.quantity + "" +
                        "</td> <td>" + item.buy_price
                        + "</td> <td>" + item.buy_price * item.quantity + "</td> </tr>";

                });

                $(".modal-content-in").html(res);
                $('#myModal').modal('show')

            },
            error: function (xhr) {
                alert(xhr.responseText);

            }

        })

    }

    function filldetail(url,data){

        addTOdatabase(url, data, 'get')
            .then((response) => {


                var data = response['data'].data;
                var table = "";

                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        table += '<tr>' +
                            '<td>' + data[i].buy_factor_id + '</td>' +
                            '<td>' + data[i].company_name + '</td>' +
                            '<td>' + data[i].store_name + '</td>' +
                            '<td>' + data[i].factor_code + '</td>' +
                            '<td>' + data[i].buy_date + '</td>' +
                            '<td>' + data[i].discount + '</td>' +
                            '<td>' + data[i].current_payment + '</td>' +
                            '<td>' + data[i].total_payment + '</td>' +
                            '<td>' +
                            '<button  id-item=' + data[i].buy_factor_id + ' class="glyphicon glyphicon-edit btn btn-info btn-xs edit_buy_factor" title="ویرایش فکتور" ></button>' + '&nbsp;' +
                            '<button  id-item=' + data[i].buy_factor_id + ' class="glyphicon glyphicon-eye-open btn btn-success btn-xs view-detail view_detail" title="جزییات فکتور"></button>' + '&nbsp;' +
                            '<button  id-item=' + data[i].buy_factor_id + ' class="glyphicon glyphicon-trash btn btn-danger btn-xs delete_buy_factor" title="حذف فکتور"></button>' +
                            "</td>"
                        '</tr>';
                    }
                } else {
                    table += '<tr><td colspan="9" class="text-center">اطلاعاتی برای نمایش وجود ندارد</td></tr>';
                }
                $(".report_icon").removeClass('fa fa-spinner fa-spin');
                $('.loading').hide();

                $('#factor_report').html(table);

                // table.column(4).visible(false);
                $(".dt-button").addClass("btn");
                $('#pagination').html(response['pagination']);


            });


    }

    $(document).ready(function () {



        $(document).off('click').on('click', '#report-btn', function () {


            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $('.loading').show();


            var company_id = $("#company_id").val();
            var _token = $('input[name="_token"]').val();
            var mount = $(".list-page option:selected").text();


           let data =  {
                _token: _token,
                 company_id: company_id,
                'mount':mount
            };
            filldetail("{{route('searchByCompany.report')}}",data);


        })

        // Pagination
        $(document).on('click', '.pagination a', function (event) {

            event.preventDefault();

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            var url = $(this).attr("href");

            var company_id = $("#company_id").val();
            var _token = $('input[name="_token"]').val();
            var mount = $(".list-page option:selected").text();


            let data =  {
                _token: _token,
                company_id: company_id,
                'mount':mount
            };
            filldetail(url,data);




        });

        $(document).on("click", ".view-detail", function () {
            showDetail($(this).attr("id-item"))
        })

        //edit detail section
        $("#factor_report").on('click', ".edit_buy_factor", function () {
            var id = $(this).attr("id-item");
            javascript:ajaxLoad("buy_factor/update/" + id);
        });

        // Delete car
        $("#factor_report").on('click', ".delete_buy_factor", function () {
            var id = $(this).attr("id-item");

            if (confirm('Are you want to delete this record?')) {
                javascript:ajaxDelete("buy_factor/delete/" + id, $('meta[name="csrf-token"]').attr('content'));
            }
        });


    })
    ;

    $(document).ready(function () {

        selectTwo();
        jalali();

    });

</script>

