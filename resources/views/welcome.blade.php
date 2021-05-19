@extends('layouts.app')

	@section('content')
<div id="app">
		@if(Auth::check())
			<app :current_user="{{ Auth::user() }}"></app>

		@else
			<app :current_user="false"></app>
		@endif
</div>
	@endsection