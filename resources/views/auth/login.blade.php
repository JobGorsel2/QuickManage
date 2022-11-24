@extends('layouts.app')

@section('content')

<div class="login-wrapper">
    <div class="login-container">
   
        <img src="{{ asset('storage/default.png') }}">

        <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-form-wr">

                
                <div><label for="email"  >{{ __('Email Address') }}</label></div>
                <div><input   id="email" type="email" class=" @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus></div>
                 
                
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                 
                <div><label for="password" > {{ __('Password') }}</label></div>
                <div><input id="password" type="password" class=" @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"></div>

                
                
                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                <div>
                <button type="submit" >
                    {{ __('Login') }}
                </button>
                </div>
                @if (Route::has('password.request'))
                <div>
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>
                @endif  
                </div>    
        </form>
    </div>
</div>
    
@endsection
