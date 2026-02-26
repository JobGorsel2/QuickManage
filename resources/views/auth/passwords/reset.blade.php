@extends('layouts.app')

@section('content')
<div class="login-wrapper"> 
    <div class="login-container">
        <img src="{{ asset('storage/logo-cm.png') }}">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="input-form-wr">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div><label for="email"  >{{ __('E-mailadres') }}</label></div>
                    <div><input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus></div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    <div><label for="password"  >{{ __('Wachtwoord') }}</label></div>
                    <div><input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password"></div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                            
                    <div><label for="password-confirm">{{ __('Wachtwoord bevestigen') }}</label></div>
                    <div><input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"></div>

                    <button type="submit" class="btn btn-primary">
                        {{ __('Wachtwoord resetten') }}
                    </button>
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