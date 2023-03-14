<div class="main-menu">
    <header class="header">
        <a href="javascript:void(0);" class="logo"><i class="ico mdi mdi-cube-outline"></i>بخش ادمین</a>
        <button type="button" class="button-close fa fa-times js__menu_close"></button>
        <div class="user">
            <a href="javascript:void(0)" class="avatar">
                <img src="{{ get_options('CompanyLogo') == null ? asset('image/unnamed.png') : asset( get_options('CompanyLogo')->value('option_value') ) }}"
                     alt=""
                     style=" ;margin-right: 6px; border: none ;margin-right: 6px;
    border: none;
    object-position: left;
    height: 49px;
    width: 65px;
    object-fit: cover;">
                <span class="status online"></span>
            </a>
            <h5 class="name text-right">
                <a href="javascript:void(0)">{{ get_options("ownerName")->value('option_value') }}</a>
            </h5>
            @if (Auth::user()->user_level == 1)
                <h5 class="position text-right">مدیر عمومی</h5>
            @elseif (Auth::user()->user_level == 2)
                <h5 class="position text-right">مدیر</h5>
            @else
                <h5 class="position text-right">کاربر عادی</h5>
        @endif
        <!-- /.name -->
            <div class="control-wrap js__drop_down">
                <i class="fa fa-caret-down js__drop_down_button"></i>
                <div class="control-list">
                    <div class="control-item">
                        <a href="javascript:ajaxLoad('{{route('user.userInfo',Auth::user()->user_id)}}')"><i
                                    class="fa fa-user"></i> حساب کاربری</a>
                    </div>
                    <div class="control-item">
                        <a href="{{url('/logout')}}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                    class="fa fa-sign-out"></i> خروج</a>
                    </div>
                    <form action="{{route('logout')}}" method="POST" id="logout-form" style="display: none;">
                        {{csrf_field()}}
                    </form>
                </div>
                <!-- /.control-list -->
            </div>
            <!-- /.control-wrap -->
        </div>
        <!-- /.user -->
    </header>
    <!-- /.header -->
    <div class="content">

        <div class="navigation">

            <!-- /.title -->
            <ul class="menu js__accordion nav" id="sidebar">
                <li class="current nav_home">
                    <a class="waves-effect " href="javascript:ajaxLoad('{{route('home.list')}}')"><i
                                class="menu-icon mdi mdi-view-dashboard"></i><span>صفحه اصلی</span></a>
                </li>

                {{--@can('isManager')--}}
                {{-- Agency Section --}}
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-home-modern"></i><span>بخش نماینده گی ها</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--customer Menu--}}
                {{--<li class="nav_user"><a href="javascript:ajaxLoad('{{route('agency.list')}}')">لیست--}}
                {{--نماینده گی ها</a></li>--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('agency.create')}}')">ثبت نماینده گی جدید</a></li>--}}
                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}
                {{--@endcan--}}


                <li>
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-package-variant"></i><span>بخش محصولات</span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">
                        {{--product Menu--}}
                        <li class="nav_user"><a href="javascript:ajaxLoad('{{route('product.list')}}')">لیست
                                محصولات </a></li>
                        <li><a href="javascript:ajaxLoad('{{route('product.create')}}')">ثبت محصول جدید </a></li>
                        <li class="nav_user"><a href="javascript:ajaxLoad('{{route('category.list')}}')">لیست کتکوری</a>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('category.create')}}')">ثبت کتگوری جدید </a></li>
                        <li class="nav_user"><a href="javascript:ajaxLoad('{{route('unit.list')}}')"> لیست واحد ها</a>
                        </li>
                        <li class="nav_user"><a href="javascript:ajaxLoad('{{route('getUnitExchangers.list')}}')"> لیست تبدیل واحد ها</a>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('unit.create')}}')">ثبت واحد جدید </a></li>
                        {{-- <li class="nav_factor"><a href="javascript:ajaxLoad('{{route('destroy_product.list')}}')">لیست محصولات خراب شده</a></li>
                         <li  ><a href="javascript:ajaxLoad('{{route('destroy_product.create')}}')">ثبت محصول خراب شده</a></li>--}}
                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>

                {{--BuyFactor Menu --}}
                <li>
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-cart-outline"></i><span>بخش خرید محصولات </span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">
                        {{--User Menu--}}
                        <li class="nav_factor"><a href="javascript:ajaxLoad('{{route('buy_factor.list')}}')">لیست
                                فاکتورهای خرید شده</a></li>

                        <li><a href="javascript:ajaxLoad('{{ route('buy_product.create') }}')">ثبت فاکتورهای خرید
                                شده</a>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('buy_factor.report')}}')">گزارشات فاکتور های
                                خریدشده</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('buy_product.report')}}')">پرداخت باقیات فکتور شرکت
                                ها</a></li>

                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>

                {{--sale_product Menu--}}
                <li class="active">
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-package-variant-closed"></i><span>بخش فروش محصولات</span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">
                        {{--sale_product Menu--}}
                        <li class="nav_sale_factor"><a href="javascript:ajaxLoad('{{route('sale_factor.list')}}')">لیست
                                فاکتورهای فروش شده</a></li>
                        <li><a href="javascript:ajaxLoad('sale_factor/create')">ثبت فاکتور فروش</a></li>

                        <li><a href="javascript:ajaxLoad('{{route('sale_factor.report')}}')"> گزارشات فاکتورهای فروش
                                شده:</a></li>
                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>

                <li>
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-factory"></i><span> بخش مشتریان و شرکت ها </span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">

                        {{--customer Menu--}}

                        <li><a href="javascript:ajaxLoad('{{route('customer.remindPayment')}}')"> بدهی مشتریان </a></li>
                        <li><a href="javascript:ajaxLoad('{{route('customer.report')}}')"> گزارش مشتریان </a></li>
                        <li class="nav_user"><a href="javascript:ajaxLoad('{{route('customer.list')}}')">لیست
                                مشتریان</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('customer.create')}}')">ثبت مشتری جدید</a></li>
                        {{--User Menu--}}
                        <li class="nav_company"><a href="javascript:ajaxLoad('{{route('company.list')}}')">لیست شرکت
                                ها</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('company.create')}}')"> ثبت شرکت جدید</a></li>


                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>

                {{--Stock--}}
                <li>
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-store"></i><span>بخش گدام ها</span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">
                        {{--User Menu--}}
                        <li class="nav_factor"><a href="javascript:ajaxLoad('{{route('store.list')}}')">لیست گدام</a>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('store.create')}}')">ثبت کدام جدید</a></li>
                        <li><a href="javascript:ajaxLoad('{{ route("store.transStaff") }}')">انتقال محصول بین گدام ها
                                :</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('stock.report')}}')">گزارشات گدام</a></li>

                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>


                {{--comapny Menu --}}



                {{--expense Menu--}}
                <li>
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-watch-export"></i><span> بخش مصارف و برداشت پول</span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">
                        {{--expense Menu--}}

                        <li><a href="javascript:ajaxLoad('{{route('catch_money.list')}}')">لیست برداشت و پرداخت پول</a>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('catch_money.create')}}')">برداشت و پرداخت پول</a>
                        </li>
                        <li class="nav_expense"><a href="javascript:ajaxLoad('{{route('expense.list')}}')">لیست مصرف
                                ها:</a></li>
                        <li><a href="javascript:ajaxLoad('expense/create')">ثبت مصرف جدید:</a></li>
                        <li class="nav_reason_pay"><a href="javascript:ajaxLoad('{{route('reason_pay.list')}}')">لیست
                                نوع مصرف ها:</a></li>

                        <li><a href="javascript:ajaxLoad('reason_pay/create')">ثبت نوع مصرف جدید:</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('expense.report')}}')">گذارشات مصارف:</a></li>

                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>


                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-watch-vibrate"></i><span>بخش مصارف تجهیزات</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('first_equipment_money.list')}}')">لیست مصارف تجهیزات</a>--}}
                {{--</li>--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('first_equipment_money.create')}}')"> ثبت مصارف تجهیزات</a>--}}
                {{--</li>--}}
                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}

                @can('isManager')
                    <li>
                        <a class="waves-effect parent-item js__control" href="#"><i
                                    class="menu-icon mdi mdi-cash-multiple"></i><span>بخش حسابات پول & صندق پول</span><span
                                    class="menu-arrow fa fa-angle-down"></span></a>
                        <ul class="sub-menu js__content nav" id="nav-sidebar">
                            {{--User Menu--}}
                            <li class="nav_factor"><a href="javascript:ajaxLoad('{{route('money_store.create')}}')">اضافه
                                    کردن حساب </a></li>
                            <li><a href="javascript:ajaxLoad('{{route('money_store.list')}}')">لیست حساب ها </a>
                            </li>
                            <li><a href="javascript:ajaxLoad('{{route('money_transfer.create')}}')">انتقال پول</a></li>
                            <li><a href="javascript:ajaxLoad('{{route('money_transfer.list')}}')">لیست انتقالات پول</a>
                            </li>
                            <li><a href="javascript:ajaxLoad('{{route('money_transfer.report')}}')">گزارشات انتقالات
                                    پول</a></li>

                        </ul>
                        <!-- /.sub-menu js__content -->
                    </li>
                @endcan
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-truck"></i><span>بخش عواید موتر</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('car.list')}}')">لیست عواید موتر</a>--}}
                {{--</li>--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('car.create')}}')">اضافه کردن عواید موتر</a>--}}
                {{--</li>--}}
                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}

                {{-- Catch Money Part --}}
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-cash"></i><span>بخش برداشت و پرداخت پول</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{----}}
                {{--<li><a href="javascript:ajaxLoad('{{route('catch_money.report')}}')">گزارشات برداشت پول</a>--}}
                {{--</li>--}}
                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}

                {{-- Add Mony to Money_Store--}}
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-cash"></i><span>بخش ثبت پول به صندوق</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('add_money.list')}}')">لیست صندق ها</a>--}}
                {{--</li>--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('add_money.create')}}')">ثبت صندق جدید</a>--}}
                {{--</li>--}}
                {{--<li>--}}
                {{--</li>--}}
                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}

                {{--income Menu--}}
                <li class="active">
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-water-percent"></i><span>گزارش عواید(مفاد)</span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">
                        {{--income Menu--}}
                        <li class="nav_income"><a href="javascript:ajaxLoad('{{route('income.list')}}')">گزارشات
                                عواید(مفاد)</a></li>
                        <!-- <li ><a href="javascript:ajaxLoad('income/create')">ثبت محصول جدید:</a></li> -->

                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>


                {{--employee reports Menu--}}
                <li class="active">
                    <a class="waves-effect parent-item js__control" href="#"><i
                                class="menu-icon mdi mdi-account-multiple"></i><span>بخش کارمندان</span><span
                                class="menu-arrow fa fa-angle-down"></span></a>
                    <ul class="sub-menu js__content nav" id="nav-sidebar">


                        <li class="nav_expense"><a href="javascript:ajaxLoad('{{route('employee.list')}}')">لیست
                                کارمندان:</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('employee.create')}}')">ثبت کارمند جدید:</a></li>
                        <li class="nav_customer"><a href="javascript:ajaxLoad('{{route('employeereport.list')}}')">لیست
                                طلب کاری کارمندان</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('employeereport.create')}}')">ثبت معاشات کارمندان:</a>
                        </li>
                        <li><a href="javascript:ajaxLoad('{{route('employeereport.paymented')}}')">لیست پرداختی معاشات
                                کارمندان</a>
                        </li>
                        <li class="nav_expense"><a href="javascript:ajaxLoad('{{route('position.list')}}')">لیست
                                مقام کارمندان:</a></li>
                        <li><a href="javascript:ajaxLoad('{{route('position.create')}}')">ثبت مقام جدید:</a></li>

                    </ul>
                    <!-- /.sub-menu js__content -->
                </li>

                {{--@if (Gate::check('isManager') || Gate::check('isAdmin'))--}}
                {{--User Menu--}}
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-account-multiple"></i><span>بخش کاربران</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--User Menu--}}
                {{----}}

                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}
                {{--@endif--}}
                @if (Gate::check('isManager'))
                    {{--User Menu--}}
                    <li>
                        <a class="waves-effect parent-item js__control" href="#"><i
                                    class="menu-icon mdi mdi-account-multiple"></i><span>بخش تنظیات</span><span
                                    class="menu-arrow fa fa-angle-down"></span></a>
                        <ul class="sub-menu js__content nav" id="nav-sidebar">
                            {{--User Menu--}}
                            <li class="nav_setting_general"><a
                                        href="javascript:ajaxLoad('{{route('options.general')}}')">تنظیات عمومی</a></li>
                            <li class="nav_setting_currencyExchanger"><a
                                        href="javascript:ajaxLoad('{{route('currencyExchanger.list')}}')">تنظیات تبدیل
                                    ارز ها</a></li>
                            <li class="nav_setting_currency"><a
                                        href="javascript:ajaxLoad('{{route('currency.list')}}')">تنظیات ارز ها</a></li>
                            <li class="nav_setting_person_information"><a
                                        href="javascript:ajaxLoad('{{route('options.personality')}}')">تنظیات اطلاعات
                                    شرکت</a></li>

                            @can('isManager')
                                <li class="nav_user"><a href="javascript:ajaxLoad('{{route('user.list')}}')">لیست
                                        کاربر</a></li>
                                <li><a href="javascript:ajaxLoad('{{route('user.create')}}')">ثبت کاربر جدید</a></li>
                                <li><a href="javascript:ajaxLoad('{{route('owner.list')}}')">لیست شرکا:</a></li>
                                <li><a href="javascript:ajaxLoad('{{route('owner.create')}}')">ثبت شریک :</a></li>
                                <li class="nav_user"><a href="javascript:ajaxLoad('{{route('backup.index')}}')">بکاب
                                        جدید</a>
                                </li>
                            @endcan
                        </ul>
                        <!-- /.sub-menu js__content -->
                    </li>
                @endif
                {{--@can('isManager')--}}
                {{--User Menu--}}
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-account-multiple"></i><span>بخش کاربران</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--User Menu--}}
                {{--<li class="nav_user"><a href="javascript:ajaxLoad('{{route('user.list')}}')">لیست کاربر</a></li>--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('user.create')}}')">ثبت کاربر جدید</a></li>--}}

                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}
                {{--@endcan--}}
                {{--@can('isAdmin')--}}
                {{--User Menu--}}
                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-account-multiple"></i><span>بخش کاربران</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--User Menu--}}
                {{--<li class="nav_user"><a href="javascript:ajaxLoad('{{route('user.list')}}')">لیست کاربر</a></li>--}}
                {{--<li><a href="javascript:ajaxLoad('{{route('user.create')}}')">ثبت کاربر جدید</a></li>--}}

                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}
                {{--@endcan--}}

                {{--owner Menu--}}
                {{--<li class="active">--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-account-circle"></i><span>بخش شرکا</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}

                {{----}}
                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}


                {{--<li>--}}
                {{--<a class="waves-effect parent-item js__control" href="#"><i--}}
                {{--class="menu-icon mdi mdi-backup-restore"></i><span>بکاب از دتابس</span><span--}}
                {{--class="menu-arrow fa fa-angle-down"></span></a>--}}
                {{--<ul class="sub-menu js__content nav" id="nav-sidebar">--}}
                {{--User Menu--}}
                {{----}}

                {{--</ul>--}}
                {{--<!-- /.sub-menu js__content -->--}}
                {{--</li>--}}


            </ul>
            <!-- /.menu js __accordion -->
        </div>
        <!-- /.navigation -->
    </div>
    <!-- /.content -->
</div>
<!-- /.main-menu -->