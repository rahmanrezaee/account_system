
@extends('layout.login.master')
@section('content')

    <div id="single-wrapper">
        <form action="#" class="frm-single">
            <div class="inside">
                <div class="title"><strong>Ninja</strong>Admin</div>
                <!-- /.title -->
                <div class="frm-title">Welcome Back</div>
                <!-- /.frm-title -->
                <a href="#" class="avatar"><img src="assets/images/avatar-2.jpg" alt=""></a>
                <p class="text-center">Enter your password to access the admin.</p>
                <div class="frm-input"><input type="password" placeholder="Password" class="frm-inp"><i class="fa fa-lock frm-ico"></i></div>
                <!-- /.frm-input -->
                <button type="submit" class="frm-submit">Login<i class="fa fa-arrow-circle-right"></i></button>
                <a href="page-login.html" class="a-link"><i class="fa fa-sign-in"></i>Not you? Login.</a>
                <div class="frm-footer">NinjaAdmin © 2016.</div>
                <!-- /.footer -->
            </div>
            <!-- .inside -->
        </form>
        <!-- /.frm-single -->
    </div><!--/#single-wrapper -->


@endsection