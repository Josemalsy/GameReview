@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">Registrate</div>
          <div class="card-body row">
            <div class="col-md-12">
              <form method="POST" enctype="multipart/form-data" action="{{ route('register') }}">
                @csrf
                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="name" class="col-md-6 col-form-label text-md-left">Nombre de usuario</label>
                        <input id="name" type="text" class="form-control input-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="email" class="col-md-6 col-form-label text-md-center">Correo electrónico</label>
                          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                          @error('email')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="password" class="col-md-6 col-form-label text-md-left">Contraseña</label>
                          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                            @error('password')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="password" class="col-md-6 col-form-label text-md-left">Confirmar contraseña</label>
                          <input type="password" class="form-control input-lg @error('password_confirmation') is-invalid @enderror" name="password_confirmation" autocomplete="password_confirmation">
                          @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="FecNac" class="col-md-6 col-form-label text-md-left">Fecha de nacimiento</label>
                          <input id="FecNac" type="date" class="form-control @error('FecNac') is-invalid @enderror" name="FecNac" value="{{ old('FecNac') }}" required autocomplete="FecNac" max={{date("Y-m-d")}}>
                          @error('FecNac')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="sexo" class="col-md-6 col-form-label text-md-left">Elige tú sexo</label>
                          <select class="form-control input-lg @error('sexo') is-invalid @enderror" name="sexo" value="{{ old('sexo') }}" autocomplete="sexo">
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                            <option Value="Otro">Otro</option>
                          </select>
                            @error('sexo')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                      </div>
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="avatar" class="col-md-6 col-form-label text-md-left">Avatar (opcional) </label>
                        <input type="file" class="input-lg @error('avatar') is-invalid @enderror" id="avatar" lang="es" name="avatar">
                          @error('avatar')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                    </div>
                  </div>

                  <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-6">
                      <button type="submit" class="btn btn-primary">
                        Registrarse
                      </button>
                    </div>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection