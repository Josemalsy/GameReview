@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Verifica tu correo electrónico</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Un correo electrónico de verificación ha sido enviado a tu dirección de correo.
                        </div>
                    @endif
                    Antes de continuar, por favor valida tu correo electrónico con un link de verificación
                    Si no has recibido el correo electrónico,
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Haz click aquí para recibir otro</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
