@extends('layouts.app')

@section('content')
    <div id="gjs"></div> {{-- 반드시 존재해야 함!! --}}
    @vite('resources/js/builder.js')
@endsection
