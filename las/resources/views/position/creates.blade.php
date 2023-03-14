@extends('layout.master')

@section('content')
     @include('management.position.form')

@endsection
@section('js')
    <script src="{{asset('assets/scripts/crud.js')}}">
    </script>
    @endsection