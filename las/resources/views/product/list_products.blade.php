@extends('layout.m')
@section('content')

  <div class="row">
        <div class="col col-md-6">




        </div>

        <div class="col col-md-6" id="searchindex" >
            <form action="" class="col col-md-5">
                <label for="search">جستجو</label>
                <input type="text" id="search" name="search" size="30" placeholder="جستجو" >
            </form>

            <form action=""  class="entityForm col col-md-1" style="float: left">
                <label for="entity">تعداد </label>
                <select name="entity" id="entity">
                    <option value="20">20</option>
                    <option value="25">25</option>
                    <option value="35">35</option>
                </select>

            </form>
        </div></div><br>
<div class="table-responsive" data-pattern="priority-columns">
        <table id="tech-companies-1" class="table table-small-font table-bordered table-striped">
        <thead>
<tr>
 
   
    <th data-priority="1">شماره</th>
    <th data-priority="3">نام</th>
    <th data-priority="1">تخلص</th>
    <th data-priority="3">مقام</th>
    <th data-priority="3">تحصیلات</th>
    <th data-priority="6">شماره</th>
    <th data-priority="6">ایمیل</th>
    <th data-priority="6">آدرس</th>
    <th data-priority="6">جنسیت</th>
    <th data-priority="6">تاریخ تولد</th>
    <th data-priority="6">حالت مدنی</th>
    <th data-priority="6">معاش</th>
    <th data-priority="6">تایم کاری</th>
    <th data-priority="6">عملیات</th>
</tr>
</thead>
<tbody>
            <tr>
                <th>GOOG <span class="co-name">Google Inc.</span></th>
                <td>597.74</td>
                <td>12:12PM</td>
                <td>14.81 (2.54%)</td>
                <td>582.93</td>
                <td>597.95</td>
                <td>597.73 x 100</td>
                <td>597.91 x 300</td>
                <td>731.10</td>
                <td>731.10</td>
                <td>731.10</td>
                <td>731.10</td>
                <td>731.10</td>
 <td><a href=""><i class="glyphicon glyphicon-edit"></i> ویرایش</a>
<a href="" ><i class="glyphicon glyphicon-trash"></i>حذف</a></td>
  </tr>
  </tbody>

        </table>
    </div>


    @endsection