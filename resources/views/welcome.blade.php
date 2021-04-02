@extends('layouts.app')

@section('content')
    @if(Auth::check())
        <app :current_user="{{ Auth::user()->id }}"></app>
    @else
        <app :current_user="false"></app>
    @endif
@endsection