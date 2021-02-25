@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">{{ __('Register') }}</div>
          <div class="card-body row">
            <div class="col-md-12">
              <form method="POST" enctype="multipart/form-data" action="{{ route('register') }}">
                @csrf
                  <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="name" class="col-md-4 col-form-label text-md-left">Nombre de usuario</label>
                          <input id="name" type="text" class="form-control  input-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="email" class="col-md-4 col-form-label text-md-center">Correo electrónico</label>
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
                        <label for="password" class="col-md-4 col-form-label text-md-left">Contraseña</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                          @error('password')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-6">
                      <div class="form-group">
                        <label for="password" class="col-md-4 col-form-label text-md-left">Confirmar contraseña</label>
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
                        <label for="FecNac" class="col-md-4 col-form-label text-md-left">Fecha de nacimiento</label>
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
                        <label for="genero" class="col-md-4 col-form-label text-md-left">Elige tú genero</label>
                        <select class="form-control input-lg @error('genero') is-invalid @enderror" name="genero" value="{{ old('genero') }}" autocomplete="genero">
                          <option value="Hombre">Hombre</option>
                          <option value="Mujer">Mujer</option>
                          <option Value="Otro">Otro</option>
                        </select>
                          @error('genero')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                      </div>
                    </div>
                  </div>
									</br>

									<div class="row">
										<div class="col-xs-12 col-sm-12 col-md-12">
											<div class="form-group">
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="customFileLang" lang="es" name="avatar">
													<label class="custom-file-label" for="customFileLang">Avatar (opcional) </label>
												</div>
											</div>
										</div>
									</div>
                  <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                      <button type="submit" class="btn btn-primary">
                        {{ __('Register') }}
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
@endsection