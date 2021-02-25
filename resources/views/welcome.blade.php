@extends('layouts.app')

@section('content')
    @if(Auth::check())
        <app :user="{{ Auth::user() }}"></app>
    @else
        <app :user="false"></app>
    @endif
@endsection