
<div class="col-md-12">

    <h3 style="text-align: center">
        {{isset($panel_title) ?$panel_title :''}}
    </h3>

    <table id="example" class="table table-striped table-bordered display" style="width:100%">
        <thead>
        <tr>
            <th >ای دی</th>
            <th>نام وتخلص</th>
            <th >معاش به فیصدی</th>
            <th >مضمون</th>
            <th >تایم کلاس</th>
            <th >تاریخ آغاز</th>
            <th >تعداشاگردان</th>
            <th >عملیات</th>

        </tr>
        </thead>
        <tfoot>
        <tr>
            <th >ای دی</th>
            <th>نام وتخلص</th>
            <th >معاش به فیصدی</th>
            <th >مضمون</th>
            <th >تایم کلاس</th>
            <th >تاریخ آغاز</th>
            <th >تعداشاگردن</th>
            <th >عملیات</th>

        </tr>
        </tfoot>
        <tbody>

        @foreach($salary as $salary)

            <tr>
                <td>{{$salary->employee_id}}</td>
                <td>{{$salary->first_name." "}}{{$salary->last_name}}</td>
                <td>{{$salary->salary}}</td>
                <td>{{$salary->subject_name}}</td>
                <td>{{$salary->start_time}}</td>
                <td>{{$salary->start_date}}</td>
                <td>{{$salary->student_count}}</td>
                <td>{{($salary->student_count* $salary->subject_payment)-$salary->discount}}</td>
                <td>
                    <a href="javascript:ajaxLoad('{{route('employeereport.empSalaryPayPercentage',$salary->emoployee_id)}}')"><span class=" btn btn-success btn-sm">پرداخت باقیات </span></a>
                </td>

            </tr>

        @endforeach
        </tbody>
    </table>
    <script>

        $(document).ready(function () {

            $('#example').dataTable();
        })
    </script>

</div>






