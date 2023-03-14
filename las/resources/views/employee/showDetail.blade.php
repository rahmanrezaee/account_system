<div class="col-md-12">

    <h3 style="text-align: center">{{isset($panel_title) ?$panel_title :''}}</h3>
    <div class="row ">

        <div class="panel panel-info">
            <div class="panel-heading"><h3>مشخصات</h3></div>
            <div class="panel-body">


                <div class="col-md-3 card">
                    <img src="{{ asset("image/empty_profile.jpg") }}" alt="">
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="list-group">
                            <a href="#" class="list-group-item disabled">نام:  </a>
                            <a href="#" class="list-group-item">تخلض</a>
                            <a href="#" class="list-group-item disabled">مقام</a>
                            <a href="#" class="list-group-item ">ایمیل</a>
                            <a href="#" class="list-group-item disabled">تاریخ استخدام</a>
                            <a href="#" class="list-group-item">جنسیت :</a>
                            <a href="#" class="list-group-item disabled">حالت  :  </a>
                            <a href="#" class="list-group-item ">معاش :  </a>
                            <a href="#" class="list-group-item disabled">حالت معاش :  </a>
                            <a href="#" class="list-group-item ">تایم کاری :  </a>
                            <a href="#" class="list-group-item disabled">تلن : </a>
                            <a href="#" class="list-group-item ">آدرس : </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="#" class="list-group-item disabled"> {{ $employee->first_name }} </a>
                            <a href="#" class="list-group-item">{{ $employee->last_name }}</a>
                            <a href="#" class="list-group-item disabled"> {{ $employee->position_id }} </a>
                            <a href="#" class="list-group-item "> {{ $employee->email }} </a>
                            <a href="#" class="list-group-item disabled"> {{ $employee->hire_date }} </a>
                            <a href="#" class="list-group-item">{{ $employee->gender }}</a>
                            <a href="#" class="list-group-item disabled"> {{ $employee->marital_status }} </a>
                            <a href="#" class="list-group-item "> {{ $employee->salary }} </a>
                            <a href="#" class="list-group-item disabled"> {{ $employee->salary_type }} </a>
                            <a href="#" class="list-group-item "> {{ $employee->shift }} </a>
                            <a href="#" class="list-group-item disabled">{{ $employee->phone }}</a>
                            <a href="#" class="list-group-item "> {{ $employee->address }} </a>


                        </div>
                    </div>

                    <div class="col-md-12">
                        <img src="" alt="">

                        <img id="myImg" src="{{ asset($employee->agreement_paper) }}" alt="Trolltunga, Norway" width="300" height="200">

                        <!-- The Modal -->
                        <div id="myModal" class="modal">

                            <!-- The Close Button -->
                            <span class="close">&times;</span>

                            <!-- Modal Content (The Image) -->
                            <img class="modal-content" id="img01">

                            <!-- Modal Caption (Image Text) -->
                            <div id="caption"></div>
                        </div>
                        <a href="javascript:ajaxLoad('{{route('employee.show_agreement_paper',$employee->employee_id)}}')"  class="btn btn-info">نمایش قرارداد </a>
                        <a href="javascript:ajaxLoad('employee')" class="btn btn-danger">برگشت </a>
                    </div>

                </div>

            </div>
        </div>



    </div>

</div>
