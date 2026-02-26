@extends('layouts.app')

@section('content')
<div class="login-wrapper"> 
    <div class="login-container">
        <img src="{{ asset('storage/logo-cm.png') }}">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="input-form-wr">
                            <div><label for="password">{{ __('Wachtwoord') }}</label></div>
                            <div><input id="password" type="password" class=" @error('password') is-invalid @enderror" name="password" required autocomplete="current-password"></div>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <div>
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Wachtwwoord vergeten?') }}
                                        </a>
                                    @endif
                                    <button type="submit">
                                        {{ __('Wachtwoord bevestingen') }}
                                    </button>

                                    
                                </div>
                        </div>
                    </form>
                </div>
    </div>
</div>         
@endsection
