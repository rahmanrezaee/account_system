@extends('layout.master')

@section('content')
     @include('management.unit.form')

@endsection
@section('js')
    <script src="{{asset('assets/scripts/crud.js')}}">
    </script>
    @endsection