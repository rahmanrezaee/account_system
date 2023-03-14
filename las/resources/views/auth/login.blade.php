
@extends('layout.login.master')
@section('content')

    <div id="single-wrapper">
        <form action="{{route('login')}}" class="frm-single"   method="POST">
            {{csrf_field()}}
            <div class="inside">
                <h4 class="text-center">سیستم مدیریت مدرن</h4>
                <!-- /.title -->
                <div class="frm-title">  وارد شدن </div>
                <!-- /.frm-title -->
                <div class="frm-input {{$errors->has('username') ?'has-error':''}}"><input type="text" placeholder="نام کاربری" class="frm-inp" onfocus="this.removeAttribute('readonly');" readonly name="username"><i class="fa fa-user frm-ico" ></i>
                    {!! $errors->first('username','<span class="help-block">:message</span>') !!}
                </div>
                <!-- /.frm-input -->
                <div class="frm-input {{$errors->has('password') ?'has-error':''}}"><input type="password" placeholder="رمز عبور" autocomplete="off" onfocus="this.removeAttribute('readonly');" readonly class="frm-inp" name="password"><i class="fa fa-lock frm-ico"></i>
                    {!! $errors->first('password','<span class="help-block">:message</span>') !!}
                </div>
                <!-- /.frm-input -->
                <div class="clearfix margin-bottom-20">
                    <div class="pull-left">
                        <div class="checkbox primary"><input type="checkbox" id="remember" name="remember"><label for="remember">مرا بخاطر بسپار</label></div>
                        <!-- /.checkbox -->
                    </div>
                    <!-- /.pull-left -->
                    <div class="pull-right"><a href="page-recoverpw.html" class="a-link"><i class="fa fa-unlock-alt"></i>فراموشی رمز عبور</a></div>
                    <!-- /.pull-right -->
                </div>
                <!-- /.clearfix -->
                <button type="submit" class="frm-submit">وارد شدن<i class="fa fa-arrow-circle-right"></i></button>
                <div class="row small-spacing">
                    <div class="col-sm-12">
                        <div class="txt-login-with txt-center">ویا وارد شدن از طریق</div>
                        <!-- /.txt-login-with -->
                    </div>
                    <!-- /.col-sm-12 -->
                    <div class="col-sm-6"><button type="button" class="btn btn-sm btn-icon btn-icon-left btn-social-with-text btn-facebook text-white waves-effect waves-light"><i class="ico fa fa-facebook"></i><span>Facebook</span></button></div>
                    <!-- /.col-sm-6 -->
                    <div class="col-sm-6"><button type="button" class="btn btn-sm btn-icon btn-icon-left btn-social-with-text btn-google-plus text-white waves-effect waves-light"><i class="ico fa fa-google-plus"></i>Google+</button></div>
                    <!-- /.col-sm-6 -->
                </div>
                <!-- /.row -->
                <a href="#" class="a-link"><i class="fa fa-key"></i>{{isset($panel_title) ?$panel_title:''}}? ثبت نام کردن.</a>
                <div class="frm-footer">{{isset($panel_title) ?$panel_title:''}} © TutiaTech.</div>
                <!-- /.footer -->
            </div>
            <!-- .inside -->
        </form>
        <!-- /.frm-single -->
    </div><!--/#single-wrapper -->


    @endsection