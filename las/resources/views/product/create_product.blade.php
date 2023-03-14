@extends('layout.master')

@section('content')
<div id="demo_div" class="demo">Demo </div>
     @include('management.product.form')

@endsection
@section('js')
    <script src="{{asset('assets/scripts/crud.js')}}">
    </script>
    @endsection