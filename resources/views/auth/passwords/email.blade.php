@extends('layouts.app')

@section('content')

<div class="login-wrapper"> 
    <div class="login-container">
        <img src="{{ asset('storage/logo-cm.png') }}">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-form-wr">

                    <div><label for="email">{{ __('E-mailadres') }}</label></div>
                    <div><input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus></div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                           
                    <div>
                        @if (Route::has('password.request'))
                        <a class=" " href="{{ route('login') }}">
                            {{ __('Inloggen?') }}
                        </a>
                        @endif 

                        <button type="submit">
                            {{ __('Link aanvragen') }}
                        </button>
                    </div>

                </div>
            </form>
            @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-block pt-3">
                    <strong>{{ $error }}</strong>
                </div>
            @endforeach
            @endif
    </div>
</div>
              
@endsection
