<h4>گزارش عواید(مفاد)</h4>

<div class="row">
    <form action="{{route('income.report_data')}}" id="income_report">

        <div class="col d-flex col-md-12" >

            <div class="col col-md-3">
                <div class="form-group" id="choose">
                    <label for="type">نحوه گزارش:</label>
                    <select id="type" name="type" class="form-control">
                        <option value="type-1">نوع</option>
                        <option value="day">روز</option>
                        <option value="week">هفته</option>
                        <option value="month">ماه</option>
                        <option value="year">سال</option>
                        <option value="bt_date">بین تاریخ</option>
                    </select>
                </div>

            </div>
            <div class="col col-md-3">
                @include('sale_factor.month')
                @include('sale_factor.year')
                @include('sale_factor.bettwen_date')
            </div>
            <div class="col col-md-3">
                <div class="form-group " id="between_date">
                    <label for="end_date" class="control-label ">تاریخ:*</label>
                        <input type="date"  value="" class="form-control  end_date" placeholder="روز/ماه/سال"
                               name="end_date">

                </div>
            </div>
            <div class="col d-flex align-items-center col-md-3">

                <button class="btn btn-info btn-rounded" id="report-btn"> گرفتن گزارش&nbsp;<i class="report_icon"></i></button>

            </div>

        </div>
    </form>

    <div class="col-md-12">


        <table id="example" class="table table-striped table-bordered display" style="width:100%">
            <thead>

            <tr>
                <th> مجموعه معاشات</th>
                <th> مجموعه مصارف</th>
                <th>مجموعه قیمت اجناس گدام</th>
                <th>مجموعه پرداخت های مشتری</th>
                <th>مجموعه درآمد(مفاد)</th>
            </tr>

            </thead>

            <tfoot id="table_footer"></tfoot>
            <tbody id="table_report">

            </tbody>

        </table>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        $('#report-btn').on('click', function (e) {
            e.preventDefault();
            var data = $("#income_report").serialize();
            var url = $("#income_report").attr('action');

            // var Post = $(this).attr('method');
            $(".report_icon").addClass('fa fa-spinner fa-spin');
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                async:false,
                cache:false,
                dataType: 'json',
                success: function (data) {


                    if (data[0].finaldata){
                        var h = "<tr>" +
                            "<td>" +
                            new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(data[0].finaldata.sum_of_salary_payment)+"</td>"+
                            "<td>" +new Intl.NumberFormat({ styled: 'currency', currency: 'AFN' }).format(data[0].finaldata.total_expense)+"</td>"+
                            "<td>" +new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(data[0].finaldata.sum_of_buy_factor)+"</td>"+
                            "<td>" +new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(data[0].finaldata.total_sale_factor)+"</td>"+
                            "<td>" +new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(data[0].finaldata.sum_of_benif)+"</td>"+
                            "</tr>";

                        $('#table_report').html(h);
                        var footer = "<tr><th>نام شریک</th><th colspan='2'>نام فیصدی شراکت</th><th colspan='3'>نام مقدار درامد</th></tr>"
                        data[0].owner.forEach(function (own) {

                            footer += "<tr>" +
                                "<td >" +own.full_name+"</td>"+
                                "<td colspan='2'>" +own.percentage+"</td>"+
                                "<td colspan='3'>" +new Intl.NumberFormat({ style: 'currency', currency: 'AFN' }).format(((data[0].finaldata.sum_of_benif*own.percentage)/100))+"</td>"+
                                "</tr>";


                        })
                        $(".report_icon").addClass('fa fa-spinner fa-spin');
                        $("#table_footer").html(footer);

                    } else {

                        var h = "<tr>" +
                            "<td colspan='6' class='text-center'>دیتا موجود نیست</td>" +
                            "</tr>";

                        $('#table_report').html(h);

                    }
                    $(".report_icon").removeClass('fa fa-spinner fa-spin');


                }
            });
        });

    });

    /** customer report js**/
    $(function () {
        $('#as_date').hide();
        $('#year').hide();
        $('#month').hide();
        $('#between_date').hide();
        $('#type').change(function () {

            if ($('#type').val() == 'month') {
                $('#between_date')
                $('#year').hide();
                $('#as_date').hide();
                $('#month').show();
            } else if ($('#type').val() == 'week') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'day') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').hide();

            } else if ($('#type').val() == 'year') {
                $('#month').hide();
                $('#as_date').hide();
                $('#between_date').hide();
                $('#year').show();
            } else if ($('#type').val() == 'bt_date') {
                $('#month').hide();
                $('#year').hide();
                $('#as_date').show();
                $('#between_date').show();
            } else {
                $('#selection').hide();
            }
        });
    });

    $(document).ready(function () {
        $(".date-picker").persianDatepicker({
            onRender: function () {
                $(".date-picker").val($(".today ").data("jdate"))
            }
        });
        selectTwo()
    });

</script>

