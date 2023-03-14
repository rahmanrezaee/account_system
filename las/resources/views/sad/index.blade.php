<div class="col-md-12">
    <h3 style="text-align: center">لیست مصارف</h3>
    
    <table id="example" class="table table-striped table-bordered display" style="width:100%">
        <thead>
        <tr>
            <th>ادیِ</th>
            <th>عنوان</th>
    
            <th>توضیحات</th>
            <th> مقدار </th>
            <th> نوغ پول</th>
            <th>  تاریخ پرداخت</th>
            <th>  مصرف کننده</th>
            <th>عملیات</th>
        
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ادیِ</th>
            <th>عنوان</th>
    
            <th>توضیحات</th>
            <th> مقدار </th>
            <th> نوغ پول</th>
            <th>  تاریخ پرداخت</th>
            <th>   مصرف کننده</th>
            <th>عملیات</th>
        
        </tr>
        </tfoot>
        <tbody>
        @foreach($expenses as $expense)
            <tr>
                <td>{{$expense->expense_id}}</td>
                <td>{{$expense->title}}</td>
                <td>{{$expense->description}}</td>
                <td>{{ number_format($expense->amount) }}</td>
                <td>{{$expense->currency}}</td>
                <td>{{$expense->pay_date}}</td>
                <td>{{$expense->first_name}}  {{$expense->last_name}}</td>
                <td>
                    <a href="javascript:ajaxLoad('{{route('expense.update',$expense->expense_id)}}')" class="btn btn-success btn-xs" id="edit_expense"><i class="glyphicon glyphicon-edit"></i></a>
                    <a href="javascript:if(confirm('Do you want delete this record?'))ajaxDelete('{{route('expense.delete',$expense->expense_id)}}','{{csrf_token()}}')" class="btn btn-danger btn-xs" id="delete_expense"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
        @endforeach
        
        </tbody>
    </table>
    <script>
        
        $(document).ready(function () {

        })
    </script>

</div>








<

